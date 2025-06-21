<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ubah nama 'buyer_id' menjadi 'user_id' agar konsisten
            $table->renameColumn('buyer_id', 'user_id');

            // Hapus kolom product_id karena akan dipindah ke tabel detail
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            // Tambahkan kolom baru untuk informasi pesanan
            $table->foreignId('store_id')->after('user_id')->constrained()->onDelete('cascade');
            $table->decimal('shipping_cost', 15, 2)->after('total_amount')->default(0);
            $table->text('shipping_address')->after('shipping_cost');
            $table->string('payment_method')->after('shipping_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('user_id', 'buyer_id');
            $table->foreignId('product_id')->after('id')->constrained()->onDelete('cascade');

            $table->dropConstrainedForeignId('store_id');
            $table->dropColumn(['shipping_cost', 'shipping_address', 'payment_method']);
        });
    }
};
