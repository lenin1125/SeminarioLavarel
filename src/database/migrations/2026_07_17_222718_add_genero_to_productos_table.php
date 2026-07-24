<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Añade el campo género aceptando solo: Hombre, Mujer o Unisex
            $table->enum('genero', ['Hombre', 'Mujer', 'Unisex'])->default('Unisex')->after('precio');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('genero');
        });
    }
};