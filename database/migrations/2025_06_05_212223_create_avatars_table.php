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
        Schema::create('avatars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Um avatar pertence a um usuário
            $table->string('path')->unique(); // Caminho para o arquivo do avatar, deve ser único
            $table->string('original_file_name')->nullable(); // Nome do arquivo original (ex: "minha_foto.jpg")
            $table->string('mime_type', 50)->nullable(); // Tipo MIME do arquivo (ex: "image/jpeg")
            $table->unsignedBigInteger('size')->nullable(); // Tamanho do arquivo em bytes
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avatars');
    }
};
