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
            // Adiciona uma chave estrangeira para a tabela 'profiles'
            // Cada avatar pertence a um único perfil, e um perfil pode ter no máximo um avatar.
            $table->foreignId('profile_id')->unique()->constrained('profiles')->onDelete('cascade');
            $table->string('path'); // Caminho interno no storage (ex: avatars/user_id.ext)
            $table->string('url'); // URL pública para acesso no navegador (ex: /storage/avatars/user_id.ext)
            $table->string('original_filename')->nullable(); // Nome original do arquivo enviado
            $table->string('mime_type')->nullable(); // Tipo MIME do arquivo (image/jpeg, image/png)
            $table->unsignedBigInteger('size')->nullable(); // Tamanho do arquivo em bytes
            $table->timestamps();
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
