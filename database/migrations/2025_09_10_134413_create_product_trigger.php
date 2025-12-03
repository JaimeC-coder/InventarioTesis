<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    public function up(): void
    {
        // // Crear función en PostgreSQL
        // DB::unprepared('
        //     CREATE OR REPLACE FUNCTION insert_product_log()
        //     RETURNS trigger AS $$
        //     BEGIN
        //         INSERT INTO logs (product_id, message, created_at, updated_at)
        //         VALUES (NEW.id, \'Producto registrado con ID: \' || NEW.id, NOW(), NOW());
        //         RETURN NEW;
        //     END;
        //     $$ LANGUAGE plpgsql;
        // ');
        // // Crear trigger que llama a la función
        // DB::unprepared('
        //     CREATE TRIGGER after_product_insert
        //     AFTER INSERT ON products
        //     FOR EACH ROW
        //     EXECUTE FUNCTION insert_product_log();
        // ');
    }

    public function down(): void
    {
        // DB::unprepared('DROP TRIGGER IF EXISTS after_product_insert ON products;');
        // DB::unprepared('DROP FUNCTION IF EXISTS insert_product_log();');
    }
};
