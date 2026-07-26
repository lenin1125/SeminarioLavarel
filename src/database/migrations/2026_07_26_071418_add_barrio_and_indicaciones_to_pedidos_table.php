<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'barrio')) {
                $table->string('barrio')->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('pedidos', 'indicaciones')) {
                $table->string('indicaciones')->nullable()->after('barrio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['barrio', 'indicaciones']);
        });
    }
};