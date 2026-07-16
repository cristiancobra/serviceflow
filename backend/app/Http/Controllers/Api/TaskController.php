<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Resources\TasksResource;
use App\Http\Requests\TaskRequest;
use App\Services\DateTimeConversionService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::with([
            'journeys.user',
            'project.company',
            'opportunity.lead',
            'opportunity.company',
            'department'
        ])
            ->orderBy('date_due', 'desc')
            ->paginate(500);

        return TasksResource::collection($tasks);
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

            return TasksResource::make($task->load('project', 'department'));
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

        return TasksResource::make(Task::with([
            'journeys.user',
            'links',
            'project.company',
            'opportunity.lead',
            'opportunity.company',
            'department'
        ])
            ->find($task->id));
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
            // Valida regra de negócio: não pode finalizar sem jornada ou com jornada aberta
            if ($request->date_conclusion) {
                $task->loadMissing('journeys');
                $journeys = $task->journeys;

                if ($journeys->isEmpty()) {
                    return response()->json([
                        'message' => 'A tarefa não pode ser finalizada sem nenhuma jornada registrada.',
                        'errors' => ['date_conclusion' => ['A tarefa não pode ser finalizada sem nenhuma jornada registrada.']],
                    ], 422);
                }

                $hasOpenJourney = $journeys->contains(fn($j) => is_null($j->end));
                if ($hasOpenJourney) {
                    return response()->json([
                        'message' => 'A tarefa não pode ser finalizada enquanto houver jornada em aberto.',
                        'errors' => ['date_conclusion' => ['A tarefa não pode ser finalizada enquanto houver jornada em aberto.']],
                    ], 422);
                }
            }

            $task->fill($request->validated());

            if (
                $request->date_conclusion ||
                $request->date_start
            ) {
                // $task->date_conclusion = $request->date_conclusion;
                $task->duration_days = DateTimeConversionService::calculateDurationDays($request->date_conclusion, $request->date_start);
            }

            $task->save();

            $updatedTask = Task::with([
                'journeys.user',
                'project.company',
                'opportunity.lead',
                'opportunity.company',
                'department'
            ])->find($task->id);

            return TasksResource::make($updatedTask);
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
        $perPage = $request->input('per_page', 500);

        $tasks = Task::with([
            'journeys.user',
            'project.company',
            'opportunity.lead',
            'opportunity.company',
            'department'
        ])
            ->where('account_id', auth()->user()->account_id)
            ->orderBy('date_due', 'desc');

        if ($status) {
            if ($status === 'done') {
                // Tarefas concluídas
                $tasks->whereNotNull('date_conclusion')
                      ->whereNull('date_canceled');
            } elseif ($status === 'canceled') {
                // Tarefas canceladas
                $tasks->whereNotNull('date_canceled');
            } else {
                // Outros status (to-do, doing, wait)
                $tasks->where('status', $status)
                      ->whereNull('date_conclusion')
                      ->whereNull('date_canceled');
            }
        } else {
            // Se não houver status, retorna todas as tarefas não canceladas
            $tasks->whereNull('date_canceled');
        }

        $filteredTasks = $tasks->paginate($perPage);

        return TasksResource::collection($filteredTasks);
    }

    /**
     * Filtra tarefas por departamento
     * 
     * @return \Illuminate\Http\Response
     */
    public function filterTasksByDepartment(Request $request)
    {
        $departmentId = $request->input('department_id');
        $perPage = $request->input('per_page', 500);

        Log::info('🔍 Filtrando tarefas por departamento', [
            'department_id' => $departmentId,
            'account_id' => auth()->user()->account_id
        ]);

        $tasks = Task::with([
            'journeys.user',
            'project.company',
            'opportunity.lead',
            'opportunity.company',
            'department'
        ])
            ->where('account_id', auth()->user()->account_id)
            ->whereNull('date_canceled')
            ->orderBy('date_due', 'desc');

        if ($departmentId) {
            $tasks->where('department_id', $departmentId);
        }

        $filteredTasks = $tasks->paginate($perPage);

        Log::info('✅ Tarefas filtradas', [
            'total' => $filteredTasks->total(),
            'count' => $filteredTasks->count()
        ]);

        return TasksResource::collection($filteredTasks);
    }

    /**
     * Filtra tarefas pela data
     * 
     * * @return \Illuminate\Http\Response
     */
    public function filterTasksByDate()
    {

        $tasks = Task::with([
            'journeys.user',
            'project.company',
            'opportunity.lead',
            'opportunity.company',
            'department'
        ])
            ->whereIn(
                'status',
                [
                    'to-do',
                    'doing'
                ]
            )
            ->orderBy('date_due', 'desc')
            ->paginate(50);

        return TasksResource::collection($tasks);
    }

    /**
     * Retorna os motivos de cancelamento
     * @return \Illuminate\Http\Response
     */
    public function getCancellationReasons()
    {
        $cancellationReasons = [
            ['id' => 1, 'reason' => 'Cliente não respondeu'],
            ['id' => 2, 'reason' => 'Cliente desistiu do projeto'],
            ['id' => 3, 'reason' => 'Mudança de requisitos pelo cliente'],
            ['id' => 4, 'reason' => 'Falta de recursos (equipe ou materiais)'],
            ['id' => 5, 'reason' => 'Prioridade alterada pelo gestor'],
            ['id' => 6, 'reason' => 'Problemas técnicos ou operacionais'],
            ['id' => 7, 'reason' => 'Tarefa duplicada ou desnecessária'],
            ['id' => 8, 'reason' => 'Orçamento insuficiente'],
            ['id' => 9, 'reason' => 'Prazo não atendido'],
            ['id' => 10, 'reason' => 'Cancelada por decisão estratégica'],
            ['id' => 11, 'reason' => 'Outros'],
        ];
    
        return response()->json(['data' => $cancellationReasons], 200);
    }

    /**
     * Display a listing of the resource by task_id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTasksByProjectId(Request  $request)
    {

        $perPage = request()->get('per_page', 20);

        $tasksQuery = Task::where('project_id', $request->project_id)
            ->with(['journeys.user', 'project', 'opportunity']);

        $totalTasksQuery = clone $tasksQuery;
        $totalTasks = $totalTasksQuery->count();

        $completedTasksQuery = clone $tasksQuery;
        $completedTasks = $completedTasksQuery->whereNotNull('date_conclusion')->count();

        $tasks = $tasksQuery->orderBy('date_start', 'desc')
            ->paginate($perPage);

        $tasks->appends(['project_id' => $request->project_id]);

        // return TasksResource::collection($tasks);
        return response()->json([
            'data' => TasksResource::collection($tasks),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
        ]);
    }

    /**
     * Display a listing of the resource by opportunity_id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTasksByOpportunityId(Request  $request)
    {
        $perPage = request()->get('per_page', 20);

        $tasks = Task::where('opportunity_id', $request->opportunity_id)
            ->with(['journeys.user', 'project', 'opportunity'])
            ->orderBy('date_start', 'desc')
            ->paginate($perPage);

        $tasks->appends(['opportunity_id' => $request->opportunity_id]);

        return TasksResource::collection($tasks);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function prioritizedTasks()
    {
        $tasks = Task::with([
            'journeys.user',
            'project.company',
            'opportunity.lead',
            'opportunity.company',
            'department'
        ])
            ->where('account_id', auth()->user()->account_id)
            ->where(function ($query) {
                // Inclui tarefas com oportunidades não canceladas
                $query->whereHas('opportunity', function ($subQuery) {
                    $subQuery->whereNull('date_canceled');
                })
                    // Ou tarefas que não têm oportunidade associada
                    ->orWhereDoesntHave('opportunity');
            })
            ->whereNull('date_canceled')
            ->where('date_conclusion', null)
            ->orderBy('date_due', 'asc')
            // ->paginate(20);
            ->get();

        return TasksResource::collection(
            $tasks
        );
    }

    public function tasksMetrics()
    {
        return TasksResource::collection(
            Task::with([
                'journeys.user',
                'project.company',
                'opportunity.lead',
                'opportunity.company'
            ])
                ->whereIn('status', [
                    'to-do',
                    'doing'
                ])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }
}
