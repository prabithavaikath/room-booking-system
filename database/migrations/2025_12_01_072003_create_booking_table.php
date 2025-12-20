<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('total_nights')->default(1);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['confirmed', 'pending', 'cancelled', 'checked_in', 'checked_out'])->default('confirmed');
            $table->text('special_requests')->nullable(); 
            $table->string('payment_status', 20)->default('pending');
            $table->string('payment_method', 20)->nullable();
            $table->string('reference_number', 50)->nullable();
            $table->string('stripe_session_id', 100)->nullable();
            $table->string('stripe_payment_intent', 100)->nullable();
            
            $table->timestamps();
            
            $table->index(['check_in_date', 'check_out_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};