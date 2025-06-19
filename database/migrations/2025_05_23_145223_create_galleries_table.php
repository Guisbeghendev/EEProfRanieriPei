<?php

// database/migrations/xxxx_xx_xx_create_galleries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date')->nullable();
            // Adicionando a coluna 'watermark_file_used' diretamente aqui
            // Será usada para registrar qual arquivo de marca d'água foi aplicado às imagens.
            $table->string('watermark_file_used')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('galleries');
    }
};
