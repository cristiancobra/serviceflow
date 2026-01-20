<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpportunityRequest;
use App\Models\Opportunity;
use App\Http\Resources\OpportunitiesResource;

class OpportunityController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $opportunities = Opportunity::with([
            'project',
            'tasks' => function ($query) {
                $query->orderBy('date_start', 'desc');
            },
        ])
            -> orderByRaw('date_canceled IS NULL DESC')
            ->orderByRaw('date_conclusion IS NULL DESC')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return OpportunitiesResource::collection($opportunities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return OpportunitiesResource
     */
    public function store(OpportunityRequest $request)
    {
        try {
            $opportunity = new Opportunity;
            $opportunity->fill($request->validated());

            $opportunity->save();

            return OpportunitiesResource::make($opportunity);
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
     * @param  \App\Models\Opportunity  $opportunity
     * @return \Illuminate\Http\Response
     */
    public function show(Opportunity $opportunity)
    {

        return OpportunitiesResource::make(Opportunity::with([
            'tasks' => function ($query) {
                $query->orderByRaw('date_conclusion IS NOT NULL ASC')
                ->orderBy('date_start', 'desc')
                ->with('journeys');
            },
            'company',
            'lead',
            'links',
        ])->find($opportunity->id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Opportunity  $opportunity
     * @return \Illuminate\Http\Response
     */
    public function update(OpportunityRequest $request, Opportunity $opportunity)
    {
        try {
            $opportunity->fill($request->validated());
            $opportunity->save();

            return OpportunitiesResource::make(Opportunity::with([
                'tasks' => function ($query) {
                    $query->orderByRaw('date_conclusion IS NOT NULL ASC')
                        ->orderBy('date_start', 'desc')
                        ->with('journeys');
                },
                'company',
                'lead',
                'links',
            ])->find($opportunity->id));
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
     * @param  \App\Models\Opportunity  $opportunity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Opportunity $opportunity)
    {
        try {
            $opportunity->delete();

            return response()->json(['message' => 'Opportunity deleted']);
        } catch (\Exception $e) {
            \Log::error('Error deleting opportunity: ' . $e->getMessage());

            return response()->json(['error' => 'Unable to delete opportunity'], 500);
        }
    }

    /** 
     * Count the number of opportunities with date_conclusion null
     * 
     * @return \Illuminate\Http\Response
     * 
     */
    public function countOpenOpportunities()
    {
        try {
            $totalOpportunities = Opportunity::whereNull('date_conclusion')->count();

            return response()->json(['totalOpportunities' => $totalOpportunities]);
        } catch (\Exception $e) {
            \Log::error('Error counting open opportunities: ' . $e->getMessage());

            return response()->json(['error' => 'Unable to count open opportunities'], 500);
        }
    }
}
