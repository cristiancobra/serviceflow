<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskRequest;
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
        $tasks = Task::with('project') // Carrega o relacionamento project
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return TaskResource::collection($tasks);
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
    public function store(TaskRequest $request)
    {
        try {
            $task = new Task;
            $task->fill($request->validated());

            $task->save();

            return TaskResource::make($task->load('project'));
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {

        return TaskResource::make(Task::with(['journeys' => function ($query) {
            $query->orderBy('start', 'desc');
        }, 'project'])
            ->find($task->id));
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
    public function update(TaskRequest $request, Task $task)
    {
        try {
            $task->fill($request->validated());

            if (
                $request->date_conclusion ||
                $request->date_start
            ) {
                // $task->date_conclusion = $request->date_conclusion;
                $task->duration_days = DateConversionService::calculateDurationDays($request->date_conclusion, $request->date_start);
            }

            $task->save();

            return TaskResource::make($task)->additional([
                'project' => $task->project,
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {

        $task->delete();

        return response()->json([
            'message' => "Tarefa excluída com sucesso",
        ], 200);
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

        return TaskResource::collection($filteredTasks);
    }

    /**
     * Filtra tarefas pela data
     * 
     * * @return \Illuminate\Http\Response
     */
    public function filterTasksByDate()
    {

        $tasks = Task::whereIn(
            'status',
            [
                'to-do',
                'doing'
            ]
        )
            ->orderBy('date_due', 'desc')
            ->paginate(50);

        return TaskResource::collection($tasks);
    }

    /**
     * Display a listing of the resource by task_id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTasksByProjectId(Request  $request)
    {

        $perPage = request()->get('per_page', 20); // Ajuste a quantidade de itens por página conforme necessário

        $tasks = Task::where('project_id', $request->project_id)
            ->with('project')
            ->orderBy('date_start', 'desc')
            ->paginate($perPage);

        $tasks->appends(['project_id' => $request->project_id]);

        return TaskResource::collection($tasks);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function prioritizedTasks()
    {
        $tasks = Task::whereIn('status', [
            'to-do',
            'doing'
        ])
            ->with('project')
            ->orderBy('date_due', 'asc')
            ->paginate(10);

        return TaskResource::collection(
            $tasks
        );
    }

    public function tasksMetrics()
    {
        return TaskResource::collection(
            Task::whereIn('status', [
                'to-do',
                'doing'
            ])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }
}
