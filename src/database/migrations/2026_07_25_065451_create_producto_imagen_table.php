<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('producto_imagen', function (Blueprint $table) {
        $table->id();
        $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
        $table->string('ruta')->nullable();
        $table->string('imagen')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_imagen');
    }
};
