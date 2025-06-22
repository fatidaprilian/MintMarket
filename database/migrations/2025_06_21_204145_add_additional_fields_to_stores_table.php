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
        Schema::table('stores', function (Blueprint $table) {
            // Tambahkan kolom yang ada di $fillable Store.php, jika belum ada
            if (!Schema::hasColumn('stores', 'phone')) {
                $table->string('phone', 20)->nullable();
            }
            if (!Schema::hasColumn('stores', 'whatsapp')) {
                $table->string('whatsapp', 20)->nullable();
            }
            if (!Schema::hasColumn('stores', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('stores', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
            if (!Schema::hasColumn('stores', 'store_type')) {
                $table->string('store_type')->nullable(); // Contoh: 'perorangan', 'bisnis'
            }
            if (!Schema::hasColumn('stores', 'operating_hours')) {
                $table->json('operating_hours')->nullable(); // Menyimpan jam operasional dalam JSON
            }
            if (!Schema::hasColumn('stores', 'instagram')) {
                $table->string('instagram')->nullable();
            }
            if (!Schema::hasColumn('stores', 'facebook')) {
                $table->string('facebook')->nullable();
            }
            if (!Schema::hasColumn('stores', 'tiktok')) {
                $table->string('tiktok')->nullable();
            }
            if (!Schema::hasColumn('stores', 'terms_and_conditions')) {
                $table->text('terms_and_conditions')->nullable();
            }
            // total_sales dihapus sesuai permintaan
            if (!Schema::hasColumn('stores', 'rating')) {
                $table->decimal('rating', 3, 2)->nullable()->default(0.00); // rating setelah terms_and_conditions atau terakhir jika tidak ada after
            }
            if (!Schema::hasColumn('stores', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'whatsapp',
                'email',
                'is_verified',
                'store_type',
                'operating_hours',
                'instagram',
                'facebook',
                'tiktok',
                'terms_and_conditions',
                'rating',
                // 'total_sales', // total_sales dihapus
                'last_active_at'
            ]);
        });
    }
};
