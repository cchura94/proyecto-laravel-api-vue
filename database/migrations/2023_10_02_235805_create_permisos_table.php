<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string("nombre", 30)->unique(); // manage_all, index_producto, listar_producto
            $table->string("action"); // listar, guardar, mostrar, modificar
            $table->string("subject"); // producto, user, pedido, cliente, categoria
            $table->text("detalle")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
