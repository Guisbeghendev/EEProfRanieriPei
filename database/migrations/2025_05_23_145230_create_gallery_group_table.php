<?php

// database/migrations/xxxx_xx_xx_create_gallery_group_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gallery_group', function (Blueprint $table) {
            $table->foreignId('gallery_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->primary(['gallery_id', 'group_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('gallery_group');
    }
};
