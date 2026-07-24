<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Eliminamos la restricción en cascada antigua
            $table->dropForeign(['usuario_id']);

            // Permitimos que usuario_id sea nulo
            $table->unsignedBigInteger('usuario_id')->nullable()->change();

            // Recreamos la relación: si se borra el usuario, el pedido PERMANECE con usuario_id = NULL
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};