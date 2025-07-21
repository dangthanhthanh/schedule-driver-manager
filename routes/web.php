<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PlanningController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Drivers
|--------------------------------------------------------------------------
*/
Route::resource('drivers', DriverController::class);

// Log trạng thái tài xế
Route::prefix('drivers/{driver}')->group(function () {
    Route::get('status-log', [DriverController::class, 'getStatusLog'])->name('drivers.status-log');
    Route::post('status-log', [DriverController::class, 'updateStatusLog'])->name('drivers.status-log.store');
});

// Danh sách trạng thái tài xế (AJAX)
Route::get('drivers/statuses', [DriverController::class, 'getAvailableStatuses'])
    ->name('drivers.statuses');

/*
|--------------------------------------------------------------------------
| Trucks
|--------------------------------------------------------------------------
*/
Route::resource('trucks', TruckController::class);

// Log trạng thái xe
Route::prefix('trucks/{truck}')->group(function () {
    Route::get('status-log', [TruckController::class, 'getStatusLog'])->name('trucks.status-log');
    Route::post('status-log', [TruckController::class, 'updateStatusLog'])->name('trucks.status-log.store');
});

// Danh sách trạng thái xe (AJAX)
Route::get('trucks/statuses', [TruckController::class, 'getAvailableStatuses'])
    ->name('trucks.statuses');

/*
|--------------------------------------------------------------------------
| Schedules
|--------------------------------------------------------------------------
*/
Route::resource('schedules', ScheduleController::class);

// Trucks in schedule
Route::prefix('schedules/{schedule}')->group(function () {
    Route::get('trucks', [ScheduleController::class, 'getScheduleTrucks'])->name('schedules.trucks');
    Route::post('trucks', [ScheduleController::class, 'addTruckToSchedule'])->name('schedules.trucks.store');
});

Route::prefix('schedule-trucks/{scheduleTruck}')->group(function () {
    Route::put('/', [ScheduleController::class, 'updateTruckInSchedule'])->name('schedules.trucks.update');
    Route::delete('/', [ScheduleController::class, 'removeTruckFromSchedule'])->name('schedules.trucks.destroy');
});

/*
|--------------------------------------------------------------------------
| Locations
|--------------------------------------------------------------------------
*/
Route::resource('locations', LocationController::class);


Route::prefix('planning')->group(function () {
    // Lịch tài xế
    Route::get('drivers', [PlanningController::class, 'driversGrid'])->name('planning.drivers');
    Route::post('drivers/cell', [PlanningController::class, 'saveDriverCell'])->name('planning.drivers.cell.save');
    Route::delete('drivers/cell', [PlanningController::class, 'deleteDriverCell'])->name('planning.drivers.cell.delete');

    // Lịch xe
    Route::get('trucks', [PlanningController::class, 'trucksGrid'])->name('planning.trucks');
    Route::post('trucks/cell', [PlanningController::class, 'saveTruckCell'])->name('planning.trucks.cell.save');
    Route::delete('trucks/cell', [PlanningController::class, 'deleteTruckCell'])->name('planning.trucks.cell.delete');

    // Lịch vận chuyển (driver-focused + cargo)
    Route::get('transport', [PlanningController::class, 'transportGrid'])->name('planning.transport');
    Route::post('transport/cell', [PlanningController::class, 'saveTransportCell'])->name('planning.transport.cell.save');
    Route::delete('transport/cell', [PlanningController::class, 'deleteTransportCell'])->name('planning.transport.cell.delete');
});
