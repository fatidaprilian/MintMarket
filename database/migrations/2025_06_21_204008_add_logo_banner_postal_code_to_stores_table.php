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
            // Menambahkan kolom 'logo' untuk profil toko
            $table->string('logo')->nullable()->after('description');

            // Menambahkan kolom 'banner' untuk banner toko
            $table->string('banner')->nullable()->after('logo');

            // Menambahkan kolom 'postal_code' terpisah untuk alamat toko
            $table->string('postal_code', 10)->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['logo', 'banner', 'postal_code']);
        });
    }
};
