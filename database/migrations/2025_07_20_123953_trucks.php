<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->string('truck_name')->unique();
            $table->string('status')->nullable();             
            $table->string('project')->nullable();
            $table->unsignedInteger('floor')->nullable();        
            $table->unsignedInteger('capacity')->nullable();      
            $table->text('description')->nullable();            
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
