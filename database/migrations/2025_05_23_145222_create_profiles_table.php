<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; // Certifique-se que esta linha está correta e existe

// O nome desta classe DEVE corresponder ao final do nome do arquivo da migração
// Ex: Se o arquivo é 2025_05_23_145222_create_profiles_table.php
// A classe deve ser 'CreateProfilesTable'
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // $table->string('avatar')->nullable(); // REMOVIDO: A coluna 'avatar' não deve mais existir aqui, pois agora está na tabela 'avatars'.
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('other_contact')->nullable();
            $table->text('ranieri_text')->nullable();
            $table->text('biography')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
