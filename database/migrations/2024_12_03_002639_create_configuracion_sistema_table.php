<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionSistemaTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        // Verifica si la tabla ya existe antes de crearla
        if (!Schema::hasTable('configuracion_sistema')) {
            Schema::create('configuracion_sistema', function (Blueprint $table) {
                $table->id(); // Crea el campo 'id' como llave primaria
                $table->tinyInteger('votaciones_abiertas')->default(0); // Campo 'votaciones_abiertas' de tipo TINYINT con valor por defecto 0 (false)
                $table->timestamps(); // Crea los campos 'created_at' y 'updated_at'
            });
        }
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        // Elimina la tabla si existe
        Schema::dropIfExists('configuracion_sistema');
    }
}
