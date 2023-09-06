<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TaskController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// JOURNEYS
Route::apiResource('journeys', JourneyController::class)
		->names('journeys');

// LEADS
Route::apiResource('leads', LeadController::class)
		->names('leads');

//PROJETOS
Route::get('projects/status', [ProjectController::class, 'getProjectsStatus']);

Route::apiResource('projects', ProjectController::class)
		->names('projects');

// SERVICES
Route::apiResource('services', ServiceController::class)
		->names('services');

//TAREFAS
Route::get('tasks/status', [TaskController::class, 'getTasksStatus'])
	->name('tasks.status');

Route::get('tasks/filter-status', [TaskController::class, 'filterTasksByStatus'])
	->name('tasks.filter-status');

Route::get('tasks/filter-date', [TaskController::class, 'filterTasksByDate'])
	->name('tasks.filter-date');

Route::apiResource('tasks', TaskController::class)
		->names('tasks');