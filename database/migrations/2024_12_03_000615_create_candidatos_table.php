<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('candidatos')) {
            Schema::create('candidatos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 255);
                $table->enum('cargo', ['personero', 'cabildante', 'contralor']);
                $table->string('foto', 255);
                $table->text('propuestas');
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
        Schema::dropIfExists('candidatos');
    }
}
