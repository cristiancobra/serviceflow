<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Http\Resources\LeadsResource;
use App\Http\Resources\LeadResource;
use App\Http\Requests\LeadCreateRequest;
use App\Http\Requests\LeadUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leads = Lead::orderBy('name', 'ASC')
            ->paginate(50);
        
        return LeadsResource::collection($leads);
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
    public function store(LeadCreateRequest $request)
    {
        try {
            $lead = new Lead;

            $lead->fill($request->all());
            $lead->account_id = 1;
            $lead->save();

            return new LeadResource($lead);
            
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
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {

        return new LeadResource(Lead::find($lead->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(LeadUpdateRequest $request, $leadId)
    {
        try {
            $lead = Lead::findOrFail($leadId);

            $lead->fill($request->all());
            $lead->save();

            return response()->json([
                'message' => "Contato $lead->name atualizado",
                'lead' => $lead,
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
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        try {
            $lead->delete();

            return response()->json([
                'message' => "Contato $lead->name deletado",
                'lead' => $lead,
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }
}
