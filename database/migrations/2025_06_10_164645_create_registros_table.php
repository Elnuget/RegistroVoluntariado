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
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voluntario_id');
            $table->date('fecha');
            $table->time('hora');
            $table->string('tipo');
            $table->string('ubicacion_desde');
            $table->string('ubicacion_hasta');
            $table->decimal('millas', 8, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('voluntario_id')
                  ->references('id')
                  ->on('voluntarios')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};
