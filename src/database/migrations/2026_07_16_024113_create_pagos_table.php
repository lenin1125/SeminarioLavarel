<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->string('metodo_pago', 50); // Nequi, Daviplata, Transferencia, Pasarela
            $table->string('comprobante_url')->nullable(); // Foto del comprobante de WhatsApp o referencia de pago
            $table->string('estado', 50)->default('Pendiente'); // Pendiente, Verificado, Rechazado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};