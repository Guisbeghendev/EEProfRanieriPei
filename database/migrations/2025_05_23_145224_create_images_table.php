<?php

// database/migrations/xxxx_xx_xx_create_images_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->constrained()->onDelete('cascade');
            $table->string('path_original');
            $table->string('path_thumb')->nullable();
            $table->string('original_file_name')->nullable(); // <-- NOVA COLUNA ADICIONADA AQUI
            $table->boolean('watermark_applied')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('images');
    }
};
