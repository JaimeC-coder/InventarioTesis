<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class MetricCatalog
{
    public static function catalog(): array
    {
        return [
            'customer.total_revenue'   => ['roles' => ['Administrador', 'ventas']],
            'customer.purchase_count'  => ['roles' => ['Administrador', 'ventas']],
            'product.total_sold'       => ['roles' => ['Administrador', 'ventas', 'almacen']], // productable_type = Sale
            'product.total_purchased'  => ['roles' => ['Administrador', 'almacen']],           // productable_type = Purchase
            'product.stock_level'      => ['roles' => ['Administrador', 'almacen']],           // desde records
            'sale.avg_ticket'          => ['roles' => ['Administrador', 'ventas']],
            'conversion.quote_to_sale_rate'         => ['roles' => ['Administrador', 'ventas']],
            'conversion.purchase_order_fulfillment' => ['roles' => ['Administrador']],
            'conversion.purchases_vs_sales_total'   => ['roles' => ['Administrador']],
        ];
    }

    public static function isAllowed(string $entity, string $metric, User $user): bool
    {
        Log::info('chatbot.check_permission', ['user' => $user->id, 'entity' => $entity, 'metric' => $metric]);
        $entry = self::catalog()[sprintf('%s.%s', $entity, $metric)] ?? null;
        if (!$entry) {
            return false; // combinación no existe en el catálogo → rechazo automático
        }

        return  $user->hasAnyRole($entry['roles']);
    }
}
