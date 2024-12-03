<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadesRecreativasTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('actividades_recreativas')) {
            Schema::create('actividades_recreativas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100);
                $table->text('descripcion')->nullable();
                $table->date('fecha')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('actividades_recreativas');
    }
}
