<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_trucks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->foreignId('truck_id')->nullable()->constrained('trucks')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('from_location_id')->nullable()->constrained('locations')->nullOnDelete(); // <-- liên kết location
            $table->foreignId('to_location_id')->nullable()->constrained('locations')->nullOnDelete(); // <-- liên kết location
            $table->string('assistant')->nullable();       // phụ xe
            $table->text('cargo_desc')->nullable();        // mô tả hàng
            $table->timestamps();
        });        
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_trucks');
    }
};
