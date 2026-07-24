<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('telefono')->nullable()->after('usuario_id');
            $table->string('departamento')->nullable()->after('telefono');
            $table->string('ciudad')->nullable()->after('departamento');
            $table->string('direccion')->nullable()->after('ciudad');
            $table->string('barrio_notas')->nullable()->after('direccion');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['telefono', 'departamento', 'ciudad', 'direccion', 'barrio_notas']);
        });
    }
};