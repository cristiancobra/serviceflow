<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Resources\TasksResource;
use App\Http\Resources\TaskResource;
use App\Services\DateConversionService;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TasksResource::collection(
            Task::whereIn('status', [
                'to-do',
                'doing'
            ])
                ->orderBy('created_at', 'desc')
                ->get()
        );
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
        $task->fill($request->all());
        $task->account_id = 1;
        $task->user_id = 1;
        // $task->contact_id = 1;
        $task->company_id = 1;
        // $task->goal_id = $request->goal_id;
        // $task->project_id = 1;
        // $task->stage_id = 1;
        // $task->name = $request->name;
        //		$task->category = $request->category;
        // $task->date_start = $request->date_start;
        // $task->date_due = $request->date_due;

        if ($request->date_conclusion) {
            $task->date_conclusion = $request->date_conclusion;
            $task->duration_days = DateConversionService::calculateDurationDays($request->date_conclusion, $request->date_start);
        }

        // $task->description = $request->description;
        // $task->status = $request->status;
        $task->save();

        return response()->json([
            'message' => "Tarefa $request->name criada",
            'task' => $task,
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

        return new TaskResource(Task::with('journeys')->find($task->id));
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
    public function getTasksStatus()
    {
        return $status = Task::getStatus();
    }

    /**
     * Filtra tarefas pelo status
     * 
     * * @return \Illuminate\Http\Response
     */
    public function filterTasksByStatus(Request $request)
    {
        $status = $request->input('status'); // Obtenha o valor do parâmetro 'status'

        $tasks = Task::orderBy('created_at', 'desc');

        if ($status) {
            $tasks->where('status', $status);
        }

        $filteredTasks = $tasks->get();

        return TasksResource::collection($filteredTasks);
    }

    /**
     * Filtra tarefas pela data
     * 
     * * @return \Illuminate\Http\Response
     */
    public function filterTasksByDate(Request $request)
    {
        $status = $request->input('date'); // Obtenha o valor do parâmetro 'status'

        $tasks = Task::orderBy('date_due', 'desc');

        // if ($status) {
        //     $tasks->where('status', $status);
        // }

        $filteredTasks = $tasks->get();

        return TasksResource::collection($filteredTasks);
    }
}
