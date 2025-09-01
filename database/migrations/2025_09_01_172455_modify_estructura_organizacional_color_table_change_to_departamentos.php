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
        Schema::table('estructura_organizacional_color', function (Blueprint $table) {
            // Primero eliminar las foreign keys y el índice único
            $table->dropForeign('eoc_estructura_fk');
            $table->dropForeign('eoc_color_fk');
            $table->dropUnique('eoc_unique');
            $table->dropColumn('color_id');
            
            // Agregar la relación con departamentos para los accesos
            $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('cascade');
            
            // Recrear la foreign key de estructura organizacional
            $table->foreign('estructura_organizacional_id', 'eoc_estructura_fk')
                  ->references('id')
                  ->on('estructura_organizacional')
                  ->onDelete('cascade');
            
            // Crear nuevo índice único
            $table->unique(['estructura_organizacional_id', 'departamento_id'], 'eoc_dept_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estructura_organizacional_color', function (Blueprint $table) {
            // Revertir los cambios
            $table->dropForeign('eoc_estructura_fk');
            $table->dropForeign(['departamento_id']);
            $table->dropUnique('eoc_dept_unique');
            $table->dropColumn('departamento_id');
            
            // Restaurar la relación con color
            $table->unsignedBigInteger('color_id');
            $table->foreign('estructura_organizacional_id', 'eoc_estructura_fk')
                  ->references('id')
                  ->on('estructura_organizacional')
                  ->onDelete('cascade');
            $table->foreign('color_id', 'eoc_color_fk')
                  ->references('id')
                  ->on('color')
                  ->onDelete('cascade');
            
            $table->unique(['estructura_organizacional_id', 'color_id'], 'eoc_unique');
        });
    }
};
