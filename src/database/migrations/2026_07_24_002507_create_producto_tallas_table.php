<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_tallas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('talla'); // Ej: "38", "39", "40" o "US 8"
            $table->integer('stock')->default(0); // Cantidad disponible
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_tallas');
    }
};