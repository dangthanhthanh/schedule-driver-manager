<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('truck_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('truck_id')
                ->constrained('trucks')
                ->cascadeOnDelete();

            $table->foreignId('status_id')
                ->constrained('truck_statuses')
                ->cascadeOnDelete();

            $table->date('date');

            $table->decimal('time_unit', 3, 1)->default(1.0);
            $table->timestamps();
            $table->unique(['truck_id', 'status_id', 'date'], 'truck_status_unique_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('truck_status_logs');
    }
};
