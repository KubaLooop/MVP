<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('absence', function (Blueprint $table) { // Podle ER
            $table->id();
            $table->foreignId('userID')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->date('dateFrom');
            $table->date('dateTo');
            $table->decimal('hours', 5, 2);
            $table->string('status')->default('pending');
            $table->string('googleCalendarID')->nullable(); // Rdy pro google API

            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence');
    }
};
