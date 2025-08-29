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
        Schema::create('empleado_finca', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('finca_id');
            $table->timestamps();
            
            // Foreign keys con nombres cortos
            $table->foreign('empleado_id', 'ef_empleado_fk')
                  ->references('id')
                  ->on('empleados')
                  ->onDelete('cascade');
                  
            $table->foreign('finca_id', 'ef_finca_fk')
                  ->references('id')
                  ->on('fincas')
                  ->onDelete('cascade');
            
            // Índice único para evitar duplicados
            $table->unique(['empleado_id', 'finca_id'], 'ef_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado_finca');
    }
};
