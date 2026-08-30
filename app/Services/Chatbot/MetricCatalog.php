<?php

namespace App\Services\Chatbot;

use App\Models\User;

class MetricCatalog
{
    public static function catalog(): array
    {
        return [
            'customer.total_revenue'   => ['roles' => ['admin', 'ventas']],
            'customer.purchase_count'  => ['roles' => ['admin', 'ventas']],
            'product.total_sold'       => ['roles' => ['admin', 'ventas', 'almacen']], // productable_type = Sale
            'product.total_purchased'  => ['roles' => ['admin', 'almacen']],           // productable_type = Purchase
            'product.stock_level'      => ['roles' => ['admin', 'almacen']],           // desde records
            'sale.avg_ticket'          => ['roles' => ['admin', 'ventas']],
            'conversion.quote_to_sale_rate'         => ['roles' => ['admin', 'ventas']],
            'conversion.purchase_order_fulfillment' => ['roles' => ['admin']],
            'conversion.purchases_vs_sales_total'   => ['roles' => ['admin']],
        ];
    }

    public static function isAllowed(string $entity, string $metric, User $user): bool
    {
        $entry = self::catalog()["{$entity}.{$metric}"] ?? null;

        if (!$entry) {
            return false; // combinación no existe en el catálogo → rechazo automático
        }

        return $user->hasAnyRole($entry['roles']);
    }
}
