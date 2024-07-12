<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
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

// Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

	// logout
	Route::post('/logout', [AuthController::class, 'logout']);

	// COMPANIES
	Route::apiResource('companies', CompanyController::class)
		->names('companies');


	// JOURNEYS
	Route::get('/journeys-by-task-id', [JourneyController::class, 'getJourneysByTaskId'])
		->name('journeysTaskId');

	Route::get('/journeys/check-open-journey', [JourneyController::class, 'checkOpenJourney']);


	Route::apiResource('journeys', JourneyController::class)
		->names('journeys');

	// LEADS
	Route::apiResource('leads', LeadController::class)
		->names('leads');

	// OPPORTUNITIES
	Route::apiResource('opportunities', OpportunityController::class)->only([
		'index', 'store', 'show', 'update', 'destroy'
	])
		->names('opportunities');

	//PROJECTS
	Route::get('projects/status', [ProjectController::class, 'getProjectsStatus']);

	Route::get('projects/prioritized', [ProjectController::class, 'prioritizedProjects'])
		->name('projects.prioritized');

	Route::apiResource('projects', ProjectController::class)
		->names('projects');

	// SERVICES
	Route::apiResource('services', ServiceController::class)
		->names('services');

	//TASKS
	Route::get('tasks/status', [TaskController::class, 'getTasksStatus'])
		->name('tasks.status');

	Route::get('tasks/filter-status', [TaskController::class, 'filterTasksByStatus'])
		->name('tasks.filter-status');

	Route::get('tasks/filter-date', [TaskController::class, 'filterTasksByDate'])
		->name('tasks.filter-date');

	Route::get('tasks/prioritized', [TaskController::class, 'prioritizedTasks'])
		->name('tasks.prioritized');

	Route::get('/tasks-by-opportunity-id', [TaskController::class, 'getTasksByOpportunityId'])
		->name('tasks.opportunity-id');

	Route::get('/tasks-by-project-id', [TaskController::class, 'getTasksByProjectId'])
		->name('tasks.task-id');

	Route::apiResource('tasks', TaskController::class)
		->names('tasks');

	// USERS
	Route::apiResource('users', UserController::class)
		->names('users');

	Route::get('/user', [UserController::class, 'currentUser']);
});
