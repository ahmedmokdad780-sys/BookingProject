<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('phone')->unique();
            $table->string('password');
            $table->date('birthdate');
            $table->enum('account_type',['owner','tenant','admin'])->default('tenant');
            $table->enum('status',['pending','approved','rejected'])->default('pending');
            $table->string('national_id_image');
            $table->string('personal_image');
            $table->boolean('is_active')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
