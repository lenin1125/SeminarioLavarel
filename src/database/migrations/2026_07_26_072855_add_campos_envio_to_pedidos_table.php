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
            if (!Schema::hasColumn('pedidos', 'telefono')) {
                $table->string('telefono', 20)->nullable();
            }
            if (!Schema::hasColumn('pedidos', 'departamento')) {
                $table->string('departamento')->nullable();
            }
            if (!Schema::hasColumn('pedidos', 'ciudad')) {
                $table->string('ciudad')->nullable();
            }
            if (!Schema::hasColumn('pedidos', 'barrio')) {
                $table->string('barrio', 150)->nullable();
            }
            if (!Schema::hasColumn('pedidos', 'direccion')) {
                $table->string('direccion')->nullable();
            }
            if (!Schema::hasColumn('pedidos', 'indicaciones')) {
                $table->string('indicaciones')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('pedidos', 'cedula') ? 'cedula' : null,
                Schema::hasColumn('pedidos', 'barrio') ? 'barrio' : null,
                Schema::hasColumn('pedidos', 'indicaciones') ? 'indicaciones' : null,
            ]));
        });
    }
};