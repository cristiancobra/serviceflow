<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpportunityRequest;
use App\Models\Opportunity;
use App\Models\Journey;
use App\Http\Resources\OpportunitiesResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
            'company',
            'lead',
            'user',
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
            
            // Carregar relações para garantir consistência nos dados
            $opportunity->load(['proposals', 'company', 'lead', 'user']);

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
                    ->with([
                        'journeys' => function ($journeyQuery) {
                            $journeyQuery->orderBy('start', 'desc')->with('user');
                        },
                        'department'
                    ]);
            },
            'company',
            'lead',
            'user',
            'links',
            'proposals.invoices.transactions',
            'proposals.invoices.lead',
            'proposals.invoices.company',
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
                        ->with([
                            'journeys' => function ($journeyQuery) {
                                $journeyQuery->orderBy('start', 'desc')->with('user');
                            },
                            'department'
                        ]);
                },
                'company',
                'lead',
                'user',
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
            Log::error('Error deleting opportunity: ' . $e->getMessage());

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
            Log::error('Error counting open opportunities: ' . $e->getMessage());

            return response()->json(['error' => 'Unable to count open opportunities'], 500);
        }
    }

    /**
     * Get only open opportunities (date_conclusion and date_canceled are null)
     * 
     * @return \Illuminate\Http\Response
     */
    public function getOpenOpportunities()
    {
        try {
            $opportunities = Opportunity::whereNull('date_conclusion')
                ->whereNull('date_canceled')
                ->orderBy('created_at', 'desc')
                ->get(['id', 'name', 'company_id', 'created_at']);

            return OpportunitiesResource::collection($opportunities);
        } catch (\Exception $e) {
            Log::error('Error fetching open opportunities: ' . $e->getMessage());

            return response()->json(['error' => 'Unable to fetch open opportunities'], 500);
        }
    }

    /**
     * Report: for each opportunity in the given year, compare the predicted
     * hourly value (accepted Proposal) against the real execution value
     * (sum of the Journeys logged on the opportunity's tasks).
     *
     * @return \Illuminate\Http\Response
     */
    public function hoursReport(Request $request)
    {
        try {
            $year = $request->query('year', date('Y'));

            $opportunities = Opportunity::with([
                'company',
                'proposals' => function ($query) {
                    $query->where('status', 'accepted')->orderByDesc('accepted_at');
                },
            ])
                ->whereYear('date_start', $year)
                ->orderBy('date_start')
                ->get();

            $opportunityIds = $opportunities->pluck('id');

            $realSecondsByOpportunity = Journey::join('tasks', 'tasks.id', '=', 'journeys.task_id')
                ->whereIn('tasks.opportunity_id', $opportunityIds)
                ->groupBy('tasks.opportunity_id')
                ->selectRaw('tasks.opportunity_id as opportunity_id, SUM(journeys.duration) as total_duration')
                ->pluck('total_duration', 'opportunity_id');

            $data = $opportunities->map(function ($opportunity) use ($realSecondsByOpportunity) {
                $proposal = $opportunity->proposals->first();

                $predictedHours = $proposal ? round($proposal->total_hours / 3600, 2) : null;
                $predictedValue = $proposal ? (float) $proposal->total_price : null;
                $predictedHourlyRate = ($predictedHours && $predictedHours > 0)
                    ? round($predictedValue / $predictedHours, 2)
                    : null;

                $realSeconds = (int) ($realSecondsByOpportunity->get($opportunity->id) ?? 0);
                $realHours = round($realSeconds / 3600, 2);
                $realValue = $predictedHourlyRate !== null ? round($realHours * $predictedHourlyRate, 2) : null;

                // Positivo = lucro (gastou menos horas/valor do que o previsto na proposta).
                // Negativo = prejuízo (gastou mais horas/valor do que o previsto).
                $differenceValue = ($predictedValue !== null && $realValue !== null)
                    ? round($predictedValue - $realValue, 2)
                    : null;
                $differencePercentage = ($predictedValue > 0 && $differenceValue !== null)
                    ? round(($differenceValue / $predictedValue) * 100, 1)
                    : null;

                $status = $opportunity->date_canceled
                    ? 'canceled'
                    : ($opportunity->date_conclusion ? 'concluded' : 'open');

                return [
                    'opportunity_id' => $opportunity->id,
                    'opportunity_name' => $opportunity->name,
                    'company_name' => optional($opportunity->company)->name,
                    'date_start' => $opportunity->date_start,
                    'status' => $status,
                    'has_accepted_proposal' => (bool) $proposal,
                    'predicted_hours' => $predictedHours,
                    'predicted_hourly_rate' => $predictedHourlyRate,
                    'predicted_value' => $predictedValue,
                    'real_hours' => $realHours,
                    'real_value' => $realValue,
                    'difference_value' => $differenceValue,
                    'difference_percentage' => $differencePercentage,
                ];
            })->values();

            return response()->json([
                'year' => (int) $year,
                'data' => $data,
                'totals' => [
                    'predicted_value' => round($data->whereNotNull('predicted_value')->sum('predicted_value'), 2),
                    'real_value' => round($data->whereNotNull('real_value')->sum('real_value'), 2),
                    'difference_value' => round($data->whereNotNull('difference_value')->sum('difference_value'), 2),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating opportunities hours report: ' . $e->getMessage());

            return response()->json(['error' => 'Unable to generate report'], 500);
        }
    }
}
