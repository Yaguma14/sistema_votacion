<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotosActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('votos_actividades')) {
            Schema::create('votos_actividades', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('usuario_id');
                $table->unsignedBigInteger('actividad_id');

                // Foreign keys
                $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
                $table->foreign('actividad_id')->references('id')->on('actividades_recreativas')->onDelete('cascade');
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('votos_actividades');
    }
}
