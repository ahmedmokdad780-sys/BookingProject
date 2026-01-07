<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // يربط التقييم بالمستخدم الذي قيم
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // يربط التقييم بالشقة
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            // يربط التقييم بحجز محدد (unique لضمان عدم تكرار التقييم لنفس الحجز)
            $table->foreignId('booking_id')->unique()->constrained()->onDelete('cascade');

            $table->integer('rating'); // من 1 إلى 5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
