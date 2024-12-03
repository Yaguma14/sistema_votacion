<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotosCandidatosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('votos_candidatos')) {
            Schema::create('votos_candidatos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('usuario_id');
                $table->unsignedBigInteger('candidato_id');

                // Foreign keys
                $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
                $table->foreign('candidato_id')->references('id')->on('candidatos')->onDelete('cascade');
                
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
        Schema::dropIfExists('votos_candidatos');
    }
}
