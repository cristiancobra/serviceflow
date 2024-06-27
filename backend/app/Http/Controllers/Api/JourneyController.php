<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JourneyResource;
use App\Models\Journey;
use App\Models\Task;
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
        $journeys = Journey::with('task') // Carrega o relacionamento project
            ->orderBy('start', 'asc')
            ->paginate(50);

        return JourneyResource::collection($journeys);

        // $perPage = request()->get('per_page', 10); // Ajuste a quantidade de itens por página conforme necessário

        // $journeys = Journey::where('task_id', $task->id)
        //     ->orderBy('start', 'desc')
        //     ->paginate($perPage);

        // return [
        //     // 'task' => TaskResource::make($taskData),
        //     'journeys' => JourneyResource::collection($journeys),
        // ];
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
    public function store(JourneyStoreRequest $request)
    {
        try {
            $journey = new Journey;
            $journey->fill($request->validated());
            $journey->save();
            $journey->updateTaskDuration();

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
            $journey->updateTaskDuration();
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
        $openJourney = Journey::whereNotNull('start')
            ->whereNull('end')
            ->first();  // Use first() para pegar a primeira jornada aberta

        if ($openJourney) {
            return response()->json([
                'hasOpenJourneys' => true,
                'openJourney' => [
                    'id' => $openJourney->id,
                    'task_id' => $openJourney->task_id,
                    'details' => $openJourney->details,
                    'start' => $openJourney->start,
                    'duration' => $openJourney->duration,
                ]
            ]);
        } else {
            return response()->json([
                'hasOpenJourneys' => false,
                'openJourney' => null
            ]);
        }
    }
}
