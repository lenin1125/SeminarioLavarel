<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'cedula')) {
                $table->string('cedula', 30)->nullable()->after('usuario_id');
            }
            if (!Schema::hasColumn('pedidos', 'barrio')) {
                $table->string('barrio', 150)->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('pedidos', 'indicaciones')) {
                $table->string('indicaciones', 255)->nullable()->after('barrio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['cedula', 'barrio', 'indicaciones']);
        });
    }
};