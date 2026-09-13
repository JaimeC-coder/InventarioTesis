<?php

namespace App\Services\Chatbot;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class MetricCatalog
{
    public static function catalog(): array
    {
        return [
            'customer.total_revenue'   => ['roles' => ['Administrador', 'Gerente']],
            'customer.purchase_count'  => ['roles' => ['Administrador', 'Gerente']],
            'product.total_sold'       => ['roles' => ['Administrador', 'Gerente', 'Jefe de Almacen','Jefe de abastecimiento']], // productable_type = Sale
            'product.total_purchased'  => ['roles' => ['Administrador', 'Jefe de Almacen','Jefe de abastecimiento']],           // productable_type = Purchase
            'product.stock_level'      => ['roles' => ['Administrador', 'Jefe de Almacen','Jefe de abastecimiento']],           // desde records
            'sale.avg_ticket'          => ['roles' => ['Administrador', 'Gerente']],
            'conversion.quote_to_sale_rate'         => ['roles' => ['Administrador', 'Gerente']],
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
