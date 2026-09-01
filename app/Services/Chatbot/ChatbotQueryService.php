<?php

namespace App\Services\Chatbot;

use App\Http\Resources\ConversionReportResource;
use App\Http\Resources\CustomerReportResource;
use App\Http\Resources\ProductReportResource;
use App\Http\Resources\SaleReportResource;
use App\Models\ChatbotQueryLog;
use App\Models\User;
use App\Repositories\ConversionRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SaleRepository;
use App\Services\Chatbot\Contracts\LlmClient;
use Illuminate\Support\Facades\Log;

class ChatbotQueryService
{
    public $lastReportData;

    public function __construct(
        private LlmClient $llmClient,
        private CustomerRepository $customerRepository,
        private ProductRepository $productRepository,
        private SaleRepository $saleRepository,
        private ConversionRepository $conversionRepository,
    ) {
    }

    public function handleUserMessage(User $user, array $conversationHistory, array $lastReport = []): array
    {
        $llmDecision = $this->llmClient->decide($conversationHistory, $this->buildTools($user));
        if ($llmDecision->toolCall) {
            $result = match ($llmDecision->toolCall['name']) {
                'queryMetric' => $this->runMetric($user, $llmDecision->toolCall['input']),
                'exportLastResult' => $this->runExport($llmDecision->toolCall['input'], $lastReport),
                default => ['error' => 'Herramienta no reconocida.'],
            };
            $text = $this->llmClient->respondWithToolResult($conversationHistory, $llmDecision->toolCall, $result);
            return [
                'reply' => $text,
                'data' => $result['data'] ?? null,
                'label' => $result['label'] ?? null, // título del reporte, para poder exportarlo después
                'file' => $result['file'] ?? null,
            ];
        }

        return ['reply' => $decision->text ?? '¿Puedes darme un poco más de detalle sobre lo que necesitas?'];
    }

    private function buildTools(User $user): array
    {
        return [
            [
                'name' => 'queryMetric',
                'description' => 'Consulta una métrica de negocio (clientes, productos, ventas o conversión entre documentos)',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'entity' => ['type' => 'string', 'enum' => $this->allowedEntities($user)],
                        'metric' => [
                            'type' => 'string',
                            'enum' => [
                                'total_revenue',
                                'purchase_count',
                                'total_sold',
                                'total_purchased',
                                'stock_level',
                                'avg_ticket',
                                'quote_to_sale_rate',
                                'purchase_order_fulfillment',
                                'purchases_vs_sales_total',
                            ],
                        ],
                        'filters' => ['type' => 'object'],
                        'sort_direction' => ['type' => 'string', 'enum' => ['asc', 'desc']],
                        'limit' => ['type' => 'integer'],
                    ],
                    'required' => ['entity', 'metric'],
                ],
            ],
            [
                'name' => 'exportLastResult',
                'description' => 'Exporta el último reporte mostrado al usuario en el formato solicitado',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'format' => ['type' => 'string', 'enum' => ['pdf', 'excel', 'txt']],
                    ],
                    'required' => ['format'],
                ],
            ],
        ];
    }

    private function allowedEntities(User $user): array
    {
        return collect(['customer', 'product', 'sale', 'conversion'])
            ->filter(fn($entity) => $user->can('chatbot.query.' . $entity))
            ->values()
            ->all();
    }

    private function runExport(array $params, array $lastReport): array
    {
        if (empty($this->lastReportData)) { // necesitarías guardar el último resultado en el Service o pasarlo por sesión
            return ['error' => 'No hay ningún reporte reciente para exportar.'];
        }

        $result = app(ReportExportService::class)->export(
            $params['format'],
            $lastReport['title'] ?? 'Reporte',
            $lastReport['data']
        );

        return ['file' => $result];
    }

    public function runMetric(User $user, array $params): array
    {
        $entity = $this->normalizeEntity($params['entity']);
        $metric = $this->normalizeMetric($params['metric']);
        Log::info('chatbot.check_permission', ['user' => $user->id, 'entity' => $entity, 'metric' => $metric]);
        if (!MetricCatalog::isAllowed($entity, $metric, $user)) {
            Log::info('chatbot.denied', ['user' => $user->id, 'entity' => $entity, 'metric' => $metric]);
            return ['error' => 'No tienes permiso para consultar ese reporte.'];
        }

        $filters = $params['filters'] ?? [];
        $direction = $params['sort_direction'] ?? 'desc';
        $limit = min($params['limit'] ?? 10, 50);
        ChatbotQueryLog::create([
            'user_id' => $user->id,
            'entity' => $entity,
            'metric' => $metric,
            'filters' => $filters,
        ]);
        // conversion.* devuelve un resumen (array plano), no una lista paginable
        if ($entity === 'conversion') {
            $summary = match ($metric) {
                'quote_to_sale_rate'         => $this->conversionRepository->quoteToSaleRate($filters),
                'purchase_order_fulfillment' => $this->conversionRepository->purchaseOrderFulfillment($filters),
                'purchases_vs_sales_total'   => $this->conversionRepository->purchasesVsSalesTotal($filters),
                default => null,
            };
            return ['data' => (new ConversionReportResource($summary))->resolve()];
        }

        $rows = match (sprintf('%s.%s', $entity, $metric)) {
            'customer.total_revenue'  => $this->customerRepository->topByRevenue($filters, $direction, $limit),
            'customer.purchase_count' => $this->customerRepository->topByPurchaseCount($filters, $direction, $limit),
            'product.total_sold'      => $this->productRepository->topSold($filters, $direction, $limit),
            'product.total_purchased' => $this->productRepository->topPurchased($filters, $direction, $limit),
            'product.stock_level'     => $this->productRepository->stockReport($filters, $limit),
            'sale.avg_ticket'         => $this->saleRepository->avgTicket($filters),
            'conversion.quote_to_sale_rate'        => $this->conversionRepository->quoteToSaleRate($filters),
            'conversion.purchase_order_fulfillment' => $this->conversionRepository->purchaseOrderFulfillment($filters),
            'conversion.purchases_vs_sales_total'   => $this->conversionRepository->purchasesVsSalesTotal($filters),
            default => null,
        };
        $resourceClass = match ($entity) {
            'customer' => CustomerReportResource::class,
            'product'  => ProductReportResource::class,
            'sale'     => SaleReportResource::class,
            default => null,
        };
        Log::info('resourceClass', ['resourceClass' => $resourceClass]);
        if (!$resourceClass) {
            return ['error' => 'No reconozco ese tipo de reporte.'];
        }

        $labels = [
            'customer.total_revenue'  => 'Clientes con más ingresos',
            'customer.purchase_count' => 'Clientes con más compras',
            'product.total_sold'      => 'Productos más vendidos',
            'product.total_purchased' => 'Productos más comprados',
            'product.stock_level'     => 'Stock por producto',
            'sale.avg_ticket'         => 'Ticket promedio de venta',
        ];
        Log::info('chatbot.query', ['user' => $user->id, 'entity' => $entity, 'metric' => $metric, 'filters' => $filters, 'direction' => $direction, 'limit' => $limit]);
        Log::info('chatbot.query.result', ['rows' => $rows]);

        return [
            'data' => $resourceClass::collection(collect($rows))->resolve(),
            'label' => $labels[sprintf('%s.%s', $entity, $metric)] ?? 'Reporte',
        ];
    }

    private function normalizeEntity(string $entity): string
    {
        return match (strtolower($entity)) {
            'productos', 'producto' => 'product',
            'clientes', 'cliente', 'Clientes' => 'customer',
            'ventas', 'venta' => 'sale',
            default => $entity,
        };
    }

    private function normalizeMetric(string $metric): string
    {
        return match (strtolower($metric)) {
            'ingresos', 'ventas_totales', 'total_revenue' => 'total_revenue',
            'compras', 'cantidad_compras', 'purchase_count' => 'purchase_count',
            'mas_vendidos', 'vendidos', 'total_vendido', 'total_sold' => 'total_sold',
            'total_comprado', 'total_purchased' => 'total_purchased',
            'nivel_stock', 'stock_level' => 'stock_level',
            'ticket_promedio', 'avg_ticket' => 'avg_ticket',
            'tasa_conversion_cotizacion_a_venta', 'quote_to_sale_rate' => 'quote_to_sale_rate',
            'cumplimiento_orden_compra', 'purchase_order_fulfillment' => 'purchase_order_fulfillment',
            'compras_vs_ventas_total', 'purchases_vs_sales_total' => 'purchases_vs_sales_total',
            default => $metric,
        };
    }
}
