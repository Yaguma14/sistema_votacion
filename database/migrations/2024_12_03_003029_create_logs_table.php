<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('logs')) {
            Schema::create('logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('usuario_id');
                $table->string('accion', 255);
                $table->text('detalles')->nullable();
                $table->timestamp('fecha_hora');
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
        Schema::dropIfExists('logs');
    }
}
