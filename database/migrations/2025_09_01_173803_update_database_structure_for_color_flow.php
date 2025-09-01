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
        // 1. Agregar relación color_id a departamentos solo si no existe
        if (!Schema::hasColumn('departamentos', 'color_id')) {
            Schema::table('departamentos', function (Blueprint $table) {
                $table->foreignId('color_id')->nullable()->constrained('color')->onDelete('set null');
            });
        }

        // 2. Recrear la tabla estructura_organizacional_color para relacionar con departamentos
        Schema::dropIfExists('estructura_organizacional_color');
        
        Schema::create('estructura_organizacional_color', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estructura_organizacional_id');
            $table->unsignedBigInteger('departamento_id');
            $table->timestamps();
            
            // Foreign keys con nombres cortos
            $table->foreign('estructura_organizacional_id', 'eoc_estructura_fk')
                  ->references('id')
                  ->on('estructura_organizacional')
                  ->onDelete('cascade');
                  
            $table->foreign('departamento_id', 'eoc_departamento_fk')
                  ->references('id')
                  ->on('departamentos')
                  ->onDelete('cascade');
            
            // Índice único para evitar duplicados
            $table->unique(['estructura_organizacional_id', 'departamento_id'], 'eoc_dept_unique');
        });

        // 3. Eliminar el campo color directo de departamentos solo si existe
        if (Schema::hasColumn('departamentos', 'color')) {
            Schema::table('departamentos', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restaurar el campo color en departamentos
        Schema::table('departamentos', function (Blueprint $table) {
            $table->string('color', 7)->nullable(); // Para códigos de color hexadecimal (#FFFFFF)
        });

        // 2. Recrear la tabla original estructura_organizacional_color
        Schema::dropIfExists('estructura_organizacional_color');
        
        Schema::create('estructura_organizacional_color', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estructura_organizacional_id');
            $table->unsignedBigInteger('color_id');
            $table->timestamps();
            
            // Foreign keys con nombres cortos
            $table->foreign('estructura_organizacional_id', 'eoc_estructura_fk')
                  ->references('id')
                  ->on('estructura_organizacional')
                  ->onDelete('cascade');
                  
            $table->foreign('color_id', 'eoc_color_fk')
                  ->references('id')
                  ->on('color')
                  ->onDelete('cascade');
            
            // Índice único para evitar duplicados
            $table->unique(['estructura_organizacional_id', 'color_id'], 'eoc_unique');
        });

        // 3. Eliminar relación color_id de departamentos
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropColumn('color_id');
        });
    }
};
