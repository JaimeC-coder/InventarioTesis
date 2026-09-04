<div class="p-2">
    <div>Description {{ $row->description }}</div>
    <div>Stock por almacén:
        <ul class="list-disc list-inside">
            @foreach ($row->stock_by_warehouse as $stock)
                <li>{{ $stock['warehouse_name'] }} - Stock: {{ $stock['quantity'] }}</li>
            @endforeach
        </ul>
    </div>


</div>
