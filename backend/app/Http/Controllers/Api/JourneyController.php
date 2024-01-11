<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JourneyResource;
use App\Models\Journey;
use Illuminate\Http\Request;
use DateTime;
use App\Services\DateConversionService;

class JourneyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {
            $journey = new Journey;
            $journey->task_id = $request->task_id;

            if ($request->details) {
                $journey->details = $request->details;
            }

            if ($request->start) {
                $journey->start = DateConversionService::convertJavascriptDate($request->start);
            } else {
                $journey->start = now();
            }

            if ($request->end) {
                $journey->end = DateConversionService::convertJavascriptDate($request->end);
                $journey->duration = DateConversionService::calculateDurationTime($journey->start, $journey->end);
            } else {
                $journey->end = null;
                $journey->duration = null;
            }

            $journey->save();
            $journey->updateTaskDuration();

            return response()->json([
                'message' => "Contato $journey->details criado",
                'journey' => $journey,
            ]);
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
    public function update(Request $request, Journey $journey)
    {
        try {

            if ($request->start) {
                $journey->start = new DateTime($request->start);
            }
            if ($request->end) {
                $journey->end = DateConversionService::convertJavascriptDate($request->end);
            }

            $journey->fill($request->all());

            if (
                $request->date_conclusion ||
                $request->date_start
            ) {
                // $journey->date_conclusion = $request->date_conclusion;
                $journey->duration_days = DateConversionService::calculateDurationDays($request->date_conclusion, $request->date_start);
            }

            $journey->save();

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
}
