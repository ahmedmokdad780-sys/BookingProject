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
        Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستأجر
    $table->foreignId('apartment_id')->constrained()->onDelete('cascade'); // الشقة
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('total_price', 10, 2);
    $table->string('payment_method')->default('cash');
    // حالات الحجز: pending (قيد المراجعة), confirmed (حالية), completed (مكتملة), cancelled (ملغية)
    $table->string('status')->default('pending');
    $table->integer('rating')->nullable(); // للتقييم لاحقاً
    $table->text('comment')->nullable();
    $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
