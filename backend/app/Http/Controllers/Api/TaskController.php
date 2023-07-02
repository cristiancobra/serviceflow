<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Resources\TasksResource;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		return TasksResource::collection(Task::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$task = new Task;
		$task->account_id = 1;
		$task->user_id = 1;
		$task->contact_id = 1;
		$task->company_id = 1;
		$task->goal_id = $request->goal_id;
		$task->project_id = 1;
		$task->stage_id = 1;
		$task->name = $request->name;
//		$task->category = $request->category;
		$task->date_start = '2021-11-15 00:00:00';
		$task->date_due = '2021-11-19 00:00:00';
		$task->date_conclusion = $request->date_conclusion;
		$task->description = $request->description;
		$task->status = $request->status;
		$task->trash = 0;
        $task->save();
		
        return response()->json([
            'message' => "Tarefa $request->name criada",
			'id' => $task->id,
			'name' => $task->name,
			'description' => $task->description,
			
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
		return new TaskResource(Task::find($task->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }
	
	/**
	 * Retorna os valores possíveis para SITUAÇÃO / STATUS 
	 */
	public function getTasksStatus() {
		return $status = Task::getStatus();
	}
}
