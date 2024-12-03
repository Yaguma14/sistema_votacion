<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicacionesForoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('publicaciones_foro')) {
            Schema::create('publicaciones_foro', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('usuario_id');
                $table->string('titulo', 255);
                $table->text('contenido');
                $table->timestamp('fecha_publicacion');
                $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
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
        Schema::dropIfExists('publicaciones_foro');
    }
}
