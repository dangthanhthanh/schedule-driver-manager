<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();   // Tên trạng thái
            $table->string('color')->nullable(); // Mã màu để hiển thị (optional)
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_statuses');
    }
};
