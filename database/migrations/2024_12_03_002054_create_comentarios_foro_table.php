<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosForoTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        // Verifica si la tabla ya existe antes de crearla
        if (!Schema::hasTable('comentarios_foro')) {
            Schema::create('comentarios_foro', function (Blueprint $table) {
                $table->id(); // Campo de ID único
                $table->foreignId('publicacion_id')->constrained('publicaciones_foro')->onDelete('cascade'); // Relación con la tabla publicaciones_foro
                $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade'); // Relación con la tabla usuarios
                $table->text('contenido'); // El contenido del comentario
                $table->timestamp('fecha_comentario')->default(DB::raw('CURRENT_TIMESTAMP')); // Fecha y hora del comentario
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
        Schema::dropIfExists('comentarios_foro');
    }
}
