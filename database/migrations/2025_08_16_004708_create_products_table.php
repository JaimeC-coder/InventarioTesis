<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('category_code')->nullable()->description('Código de la categoría del producto osea el CP');
            $blueprint->string('code')->nullable()->description('Código del producto osea el CODIGO');
            $blueprint->string('barcode')->nullable();
            $blueprint->text('description')->nullable();
            $blueprint->decimal('price_sale_regular', 15, 6)->default(0.000000);
            $blueprint->decimal('price_sale_a1', 15, 6)->default(0.000000);
            $blueprint->decimal('price_purchase', 15, 6)->default(0.000000);
            $blueprint->uuid('uuid')->unique();
            $blueprint->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $blueprint->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade')->description('Proveedor principal del producto');
            $blueprint->integer('stock')->default(0);
            $blueprint->timestamps();
            $blueprint->integer('min_stock')->default(0);
            $blueprint->foreignId('product_base_id')->nullable()->constrained('products')->onDelete('cascade');
            $blueprint->foreignId('unit_id')->nullable()->constrained('units')->onDelete('cascade'); //Caja ,Docena,Unidad
            $blueprint->foreignId('measure_id')->nullable()->constrained('measures')->onDelete('cascade');
            $blueprint->boolean('is_active_product')->default(false)->description('Indica si el producto está activo o inactivo para la venta o compra.');
            /**
             * product_base_id: referencia al producto base
             * Un producto base es un producto que se van a desprender otros productos
             *
             * unidades: 1 caja, 1 paquete, 1 docena, 1 litro, 1 kilo
             * medida: kg ,Ml,
             */
            $blueprint->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
