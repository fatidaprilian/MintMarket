<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Menambahkan kolom harga flash sale setelah kolom original_price
            // Dibuat nullable agar tidak semua produk harus punya harga flash sale.
            $table->decimal('flash_sale_price', 15, 2)->nullable()->after('original_price');

            // Menambahkan kolom waktu berakhirnya flash sale
            // Dibuat nullable agar bisa di-set hanya saat dibutuhkan.
            $table->timestamp('flash_sale_end_date')->nullable()->after('flash_sale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('flash_sale_price');
            $table->dropColumn('flash_sale_end_date');
        });
    }
};
