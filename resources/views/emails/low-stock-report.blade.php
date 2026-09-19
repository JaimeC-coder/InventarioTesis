@component('mail::message')
# Reporte de stock bajo

Se detectaron productos cuya cantidad, en al menos un almacén, está en o por debajo de su stock mínimo.

@foreach ($sections as $section)
## {{ $section['warehouse_name'] }} — {{ $section['supplier']->name }}

@component('mail::table')
| Producto | Código | Stock actual | Stock mínimo |
| :------- | :----- | -----------: | -----------: |
@foreach ($section['items'] as $item)
| {{ $item->product->name }} | {{ $item->product_code }} | {{ $item->quantity }} | {{ $item->product->min_stock }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => $section['purchase_url']])
Crear orden de compra
@endcomponent

---
@endforeach

Este es un reporte automático generado por {{ config('app.name') }}. El enlace de cada orden de compra expira en 48 horas.

{{ config('app.name') }}
@endcomponent
