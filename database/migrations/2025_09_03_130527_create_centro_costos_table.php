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
        Schema::create('centro_costos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->boolean('estado')->default(true);
            $table->string('grupo', 100)->nullable();
            $table->foreignId('tipo_contrato_id')->constrained('tipo_contrato')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centro_costos');
    }
};
