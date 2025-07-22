<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PlanningController;
use App\Models\ScheduleTruck;

/*
|--------------------------------------------------------------------------
| API Routes (JSON)
|--------------------------------------------------------------------------
| Base URL: /api/v1/...
*/

Route::prefix('v1')->group(function () {

    // Drivers
    Route::get('drivers/statuses', [DriverController::class, 'getAvailableStatuses']);
    Route::get('drivers/{driver}/status-log', [DriverController::class, 'getStatusLog']);
    Route::post('drivers/{driver}/status-log', [DriverController::class, 'updateStatusLog']);
    Route::apiResource('drivers', DriverController::class);

    // Trucks
    Route::get('trucks/statuses', [TruckController::class, 'getAvailableStatuses']);
    Route::get('trucks/{truck}/status-log', [TruckController::class, 'getStatusLog']);
    Route::post('trucks/{truck}/status-log', [TruckController::class, 'updateStatusLog']);
    Route::apiResource('trucks', TruckController::class);

    // Schedules
    Route::get('schedules/{schedule}/trucks', [ScheduleController::class, 'getScheduleTrucks']);
    Route::post('schedules/{schedule}/trucks', [ScheduleController::class, 'addTruckToSchedule']);
    Route::put('schedule-trucks/{scheduleTruck}', [ScheduleController::class, 'updateTruckInSchedule']);
    Route::delete('schedule-trucks/{scheduleTruck}', [ScheduleController::class, 'removeTruckFromSchedule']);
    Route::apiResource('schedules', ScheduleController::class);

    // Locations
    Route::apiResource('locations', LocationController::class);

    // Planning grid endpoints (JSON)
    Route::get('planning/drivers', [PlanningController::class, 'driversGrid']);
    Route::post('planning/drivers/cell', [PlanningController::class, 'saveDriverCell']);
    Route::delete('planning/drivers/cell', [PlanningController::class, 'deleteDriverCell']);

    Route::get('planning/trucks', [PlanningController::class, 'trucksGrid']);
    Route::post('planning/trucks/cell', [PlanningController::class, 'saveTruckCell']);
    Route::delete('planning/trucks/cell', [PlanningController::class, 'deleteTruckCell']);

    Route::get('planning/transport', [PlanningController::class, 'transportGrid']);
    Route::post('planning/transport/cell', [PlanningController::class, 'saveTransportCell']);
    Route::delete('planning/transport/cell', [PlanningController::class, 'deleteTransportCell']);

    // Single schedule-truck detail (for modal editing)
    Route::get('schedule-truck/{scheduleTruck}', function (ScheduleTruck $scheduleTruck) {
        $scheduleTruck->load(['truck','driver','toLocation','schedule']);
        return [
            'id'             => $scheduleTruck->id,
            'schedule_id'    => $scheduleTruck->schedule_id,
            'truck_id'       => $scheduleTruck->truck_id,
            'driver_id'      => $scheduleTruck->driver_id,
            'to_location_id' => $scheduleTruck->to_location_id,
            'assistant'      => $scheduleTruck->assistant,
            'cargo_desc'     => $scheduleTruck->cargo_desc,
            'truck_name'     => $scheduleTruck->truck?->truck_name,
            'driver_name'    => $scheduleTruck->driver?->name,
            'location_name'  => $scheduleTruck->toLocation?->name,
            'date'           => optional($scheduleTruck->schedule && $scheduleTruck->schedule->date ? $scheduleTruck->schedule->date : null)?->toDateString(),
        ];
    });
});