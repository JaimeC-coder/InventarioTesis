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
    public function __construct(
        private LlmClient $llmClient,
        private CustomerRepository $customerRepository,
        private ProductRepository $productRepository,
        private SaleRepository $saleRepository,
        private ConversionRepository $conversionRepository,
    ) {
    }

    public function handleUserMessage(User $user, array $conversationHistory): array
    {
        $decision = $this->llmClient->decide($conversationHistory, $this->buildTools($user));
        if ($decision->toolCall) {
            $result = $this->runMetric($user, $decision->toolCall['input']);
            $text = $this->llmClient->respondWithToolResult($conversationHistory, $decision->toolCall, $result);
            return ['reply' => $text, 'data' => $result['data'] ?? null];
        }

        return ['reply' => $decision->text ?? 'No entendí la consulta.'];
    }

    private function buildTools(User $user): array
    {
        return [[
            'name' => 'queryMetric',
            'description' => 'Consulta una métrica de negocio (clientes, productos, ventas o conversión entre documentos)',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'entity' => ['type' => 'string', 'enum' => $this->allowedEntities($user)],
                    'metric' => ['type' => 'string'],
                    'filters' => ['type' => 'object'],
                    'sort_direction' => ['type' => 'string', 'enum' => ['asc', 'desc']],
                    'limit' => ['type' => 'integer'],
                ],
                'required' => ['entity', 'metric'],
            ],
        ]];
    }

    private function allowedEntities(User $user): array
    {
        return collect(['customer', 'product', 'sale', 'conversion'])
            ->filter(fn($entity) => $user->can('chatbot.query.' . $entity))
            ->values()
            ->all();
    }

    public function runMetric(User $user, array $params): array
    {
        $entity = $this->normalizeEntity($params['entity']);
        $metric = $this->normalizeMetric($params['metric']);
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
            return ['data' => new ConversionReportResource($summary)];
        }

        $rows = match (sprintf('%s.%s', $entity, $metric)) {
            'customer.total_revenue'  => $this->customerRepository->topByRevenue($filters, $direction, $limit),
            'customer.purchase_count' => $this->customerRepository->topByPurchaseCount($filters, $direction, $limit),
            'product.total_sold'      => $this->productRepository->topSold($filters, $direction, $limit),
            'product.total_purchased' => $this->productRepository->topPurchased($filters, $direction, $limit),
            'product.stock_level'     => $this->productRepository->stockReport($filters, $limit),
            'sale.avg_ticket'         => $this->saleRepository->avgTicket($filters),
            default => null,
        };
        $resourceClass = match ($entity) {
            'customer' => CustomerReportResource::class,
            'product'  => ProductReportResource::class,
            'sale'     => SaleReportResource::class,
        };

        return ['data' => $resourceClass::collection(collect($rows))];
    }

    private function normalizeEntity(string $entity): string
    {
        return match (strtolower($entity)) {
            'productos', 'producto' => 'product',
            'clientes', 'cliente' => 'customer',
            'ventas', 'venta' => 'sale',
            default => $entity,
        };
    }

    private function normalizeMetric(string $metric): string
    {
        return match (strtolower($metric)) {
            'ingresos', 'total_revenue' => 'total_revenue',
            'cantidad_compras', 'purchase_count' => 'purchase_count',
            'total_vendido', 'total_sold' => 'total_sold',
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
