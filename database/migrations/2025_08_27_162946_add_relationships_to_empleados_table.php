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
        Schema::table('empleados', function (Blueprint $table) {
            // Relación uno a muchos con tipo_contrato
            $table->foreignId('tipo_contrato_id')->constrained('tipo_contrato')->onDelete('restrict');
            
            // Relación uno a muchos con estructura_organizacional
            $table->foreignId('estructura_organizacional_id')->constrained('estructura_organizacional')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['tipo_contrato_id']);
            $table->dropForeign(['estructura_organizacional_id']);
            $table->dropColumn(['tipo_contrato_id', 'estructura_organizacional_id']);
        });
    }
};
