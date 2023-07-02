<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProjectController;
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

// LEADS
Route::apiResource('leads', LeadController::class)
		->names('leads');

//PROJETOS
Route::get('projects/status', [ProjectController::class, 'getProjectsStatus']);

Route::apiResource('projects', ProjectController::class)
		->names('projects');

//TAREFAS
Route::get('tasks/status', [TaskController::class, 'getTasksStatus']);

Route::apiResource('tasks', TaskController::class)
		->names('tasks');