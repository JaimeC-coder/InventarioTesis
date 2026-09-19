<?php

namespace App\Console\Commands;

use App\Mail\LowStockReportMail;
use App\Models\User;
use App\Services\LowStockReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SendLowStockReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:send-low-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía a los administradores un reporte de productos con stock por debajo del mínimo, agrupado por almacén y proveedor.';

    private const TOKEN_TTL_HOURS = 48;

    /**
     * Execute the console command.
     */
    public function handle(LowStockReportService $lowStockReportService): int
    {
        $grouped = $lowStockReportService->groupedByWarehouseAndSupplier();
        if ($grouped->isEmpty()) {
            $this->info('No hay productos con stock bajo el mínimo. No se envía reporte.');
            return self::SUCCESS;
        }

        $sections = collect();
        foreach ($grouped as $bySupplier) {
            foreach ($bySupplier as $supplierId => $items) {
                // Sin proveedor no se puede armar una orden de compra, se omite la sección.
                if (!$supplierId) {
                    continue;
                }

                $token = (string) Str::uuid();
                $expiresAt = now()->addHours(self::TOKEN_TTL_HOURS);
                Cache::put(
                    'low-stock-report:' . $token,
                    [
                        'warehouse_uuid' => $items->first()->warehouse->uuid,
                        'supplier_uuid' => $items->first()->product->supplier->uuid,
                        'product_ids' => $items->pluck('product_id')->all(),
                    ],
                    $expiresAt
                );
                $sections->push([
                    'warehouse_name' => $items->first()->warehouse_name,
                    'supplier' => $items->first()->product->supplier,
                    'items' => $items,
                    'purchase_url' => URL::temporarySignedRoute(
                        'admin.purchases.from-report',
                        $expiresAt,
                        ['token' => $token]
                    ),
                ]);
            }
        }

        if ($sections->isEmpty()) {
            $this->info('Todos los productos con stock bajo carecen de proveedor asignado. No se envía reporte.');
            return self::SUCCESS;
        }

        $recipients = User::role('Administrador')->pluck('email')->filter()->all();
        if (empty($recipients)) {
            $this->warn('No hay usuarios con rol admin con email registrado. No se envía reporte.');
            return self::FAILURE;
        }

        Mail::to($recipients)->queue(new LowStockReportMail($sections));
        $this->info(sprintf(
            'Reporte de stock bajo encolado para: %s (%d secciones)',
            implode(', ', $recipients),
            $sections->count()
        ));

        return self::SUCCESS;
    }
}
