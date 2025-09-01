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
        Schema::table('departamentos', function (Blueprint $table) {
            // Eliminar el campo color directo ya que ahora usamos la relación con la tabla color
            $table->dropColumn('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            // Restaurar el campo color
            $table->string('color', 7)->nullable(); // Para códigos de color hexadecimal (#FFFFFF)
        });
    }
};
