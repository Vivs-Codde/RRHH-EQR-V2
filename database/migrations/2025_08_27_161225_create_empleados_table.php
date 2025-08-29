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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('idempleado_As2', 10);
            $table->boolean('estado_rrhh')->default(true);
            $table->boolean('estado_As2')->default(true);
            $table->string('nombre_As2', 20);
            $table->string('apellido_As2', 20);
            $table->date('fechaNacimiento_As2');
            $table->string('contacto', 10);
            $table->string('discapacidad_As2', 20);
            $table->integer('porcentaje_discapacidad_As2');
            $table->date('fechaIngreso_As2');
            $table->date('fechaSalida_As2')->nullable();
            $table->string('estructuraCosto_As2', 255);
            $table->date('fechaIngreso_rrhh')->nullable();
            $table->date('fechaSalida_rrhh')->nullable();
            $table->string('idSrv66', 255);
            $table->string('idSrv90', 255);
            $table->string('idAreas', 255);
            $table->string('tipoUserBiometrico', 255);
            $table->string('foto_perfil', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
