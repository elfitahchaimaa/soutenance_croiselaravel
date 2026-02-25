<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colocation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colocation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['owner', 'member'])->default('member');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable(); // null = still active member
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colocation_user');
    }
};
