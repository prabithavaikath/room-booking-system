<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->enum('type', ['Single', 'Double', 'Suite']);
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->integer('capacity')->default(1);
            $table->text('amenities')->nullable();
            $table->boolean('availability_status')->default(true);
            $table->timestamps();
            
            // Add index for better performance
            $table->index(['type', 'availability_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};