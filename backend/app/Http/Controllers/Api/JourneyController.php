<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JourneyResource;
use App\Models\Journey;
use Illuminate\Http\Request;
use App\Http\Requests\JourneyStoreRequest;

class JourneyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $journeys = Journey::with([
            'user',
            'task',
            'task.project',
            'task.opportunity',
        ])
            ->orderBy('start', 'desc')
            ->paginate(50);

        return JourneyResource::collection($journeys);

        // $perPage = request()->get('per_page', 10); // Ajuste a quantidade de itens por página conforme necessário

        // $journeys = Journey::where('task_id', $task->id)
        //     ->orderBy('start', 'desc')
        //     ->paginate($perPage);

        // return [
        //     // 'task' => TasksResource::make($taskData),
        //     'journeys' => JourneyResource::collection($journeys),
        // ];
    }

    /**
     * Filter journeys by various criteria
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filterJourneys(Request $request)
    {
        $filter = $request->input('filter');

        $journeys = Journey::with([
            'user',
            'task',
            'task.project',
            'task.opportunity',
        ]);

        switch ($filter) {
            case 'open':
                $journeys->whereNull('end');
                break;
            
            case 'closed':
                $journeys->whereNotNull('end');
                break;
            
            case 'today':
                $journeys->whereDate('start', now()->toDateString());
                break;
            
            case 'week':
                $journeys->whereBetween('start', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]);
                break;
            
            case 'month':
                $journeys->whereMonth('start', now()->month)
                    ->whereYear('start', now()->year);
                break;
        }

        $filteredJourneys = $journeys->orderBy('start', 'desc')->paginate(50);

        return JourneyResource::collection($filteredJourneys);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JourneyStoreRequest $request)
    {
        try {
            $journey = new Journey;
            $journey->fill($request->validated());
            $journey->save();
            $journey->updateAssociatedTaskDuration();

            // Carregar as relações antes de retornar
            $journey->load(['user', 'task', 'task.project', 'task.opportunity']);

            return new JourneyResource($journey);
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
     * @param  \App\Models\Journey  $journey
     * @return \Illuminate\Http\Response
     */
    public function show(Journey $journey)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Journey  $journey
     * @return \Illuminate\Http\Response
     */
    public function edit(Journey $journey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Journey  $journey
     * @return \Illuminate\Http\Response
     */
    public function update(JourneyStoreRequest $request, Journey $journey)
    {
        try {
            $journey->fill($request->validated());
            $journey->save();
            $journey->updateAssociatedTaskDuration();

            // Carregar as relações antes de retornar
            $journey->load(['user', 'task', 'task.project', 'task.opportunity']);

            return JourneyResource::make($journey);
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
     * @param  \App\Models\Journey  $journey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Journey $journey)
    {
        try {
            // Verifique se o usuário tem permissão para excluir a jornada, por exemplo, se ele é o criador da jornada
            // Você pode adicionar lógica de autorização aqui

            // Remova a jornada da base de dados
            $journey->delete();

            return response()->json(['message' => 'Jornada excluída com sucesso'], 200);
        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta de erro adequada
            return response()->json(['message' => 'Falha ao excluir a jornada', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the resource by task_id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getJourneysByTaskId(Request  $request)
    {

        $perPage = request()->get('per_page', 20); // Ajuste a quantidade de itens por página conforme necessário

        $journeys = Journey::where('task_id', $request->task_id)
            ->with(['user', 'task', 'task.project', 'task.opportunity'])
            ->orderBy('start', 'desc')
            ->paginate($perPage);

        $journeys->appends(['task_id' => $request->task_id]);

        return JourneyResource::collection($journeys);
    }

    /**
     * Check if there are open journeys
     *
     * @return \Illuminate\Http\Response
     */
    public function checkOpenJourney()
    {
        $openJourney = Journey::getOpenJourney();

        if ($openJourney) {
            return response()->json([
                'hasOpenJourneys' => true,
                'openJourney' => [
                    'id' => $openJourney->id,
                    'task_id' => $openJourney->task_id,
                    'name' => $openJourney->task->name,
                    'details' => $openJourney->details,
                    'start' => $openJourney->start,
                    'duration' => $openJourney->duration,
                    'opportunity_id' => $openJourney->task->opportunity_id,
                    'project_id' => $openJourney->task->project_id,
                ]
            ]);
        } else {
            return response()->json([
                'hasOpenJourneys' => false,
                'openJourney' => null
            ]);
        }
    }

    /**
     * Get the last 5 journeys of the authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    public function getRecentJourneys(Request $request)
    {
        $userId = $request->user()->id;
        
        $recentJourneys = Journey::where('user_id', $userId)
            ->whereNotNull('end') // Apenas jornadas finalizadas
            ->with(['task', 'task.opportunity', 'task.project'])
            ->orderBy('end', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($journey) {
                $task = $journey->task;
                return [
                    'id' => $journey->id,
                    'task_id' => $journey->task_id,
                    'task_name' => $task ? $task->name : 'Tarefa sem nome',
                    'opportunity_id' => $task ? $task->opportunity_id : null,
                    'project_id' => $task ? $task->project_id : null,
                    'end' => $journey->end,
                ];
            });

        return response()->json($recentJourneys);
    }
}
