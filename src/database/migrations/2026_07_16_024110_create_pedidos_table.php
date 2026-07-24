<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            // Apunta a la tabla users (Laravel asume 'users' al poner constrained() sin argumentos)
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade'); 
            $table->string('estado', 50)->default('Pendiente de pago'); // Pendiente de pago, Confirmado, Enviado, etc.
            $table->decimal('total', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};