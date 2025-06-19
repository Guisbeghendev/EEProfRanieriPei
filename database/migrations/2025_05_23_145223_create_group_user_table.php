<?php

// database/migrations/xxxx_xx_xx_create_group_user_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('group_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'group_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('group_user');
    }
};
