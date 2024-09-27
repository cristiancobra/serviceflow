<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\ProposalItem;
use App\Http\Resources\ProposalResource;
use App\Http\Requests\ProposalRequest;

class ProposalController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProposalRequest $request)
    {
        try {
            $proposal = new Proposal;
            $proposal->fill($request->all());
            $proposalTotalHours = 0;
            $proposalThirdPartyCost = 0;
            $proposalTotalDiscount = 0;
            $propoposalTotalPrice = 0;
            $services = $request->input('services', []);
            $proposalItems = [];
            foreach ($services as $serviceData) {
                $serviceModel = Service::find($serviceData['id']);
                if ($serviceModel) {
                    // $laborHoursTotal = $serviceModel->labor_hours * $serviceData['quantity'];
                    // $priceTotal = $serviceData['price'] * $serviceData['quantity'];
                    // $profitTotal = $serviceModel->profit * $serviceData['quantity'];

                    // proposal data
                    $proposalTotalHours += $serviceModel->labor_hours * $serviceData['quantity'];
                    $proposalThirdPartyCost += $serviceModel->third_party_cost * $serviceData['quantity'];
                    $proposalTotalDiscount += $serviceData['quantity'] * $serviceModel['discount'];
                    $propoposalTotalPrice += $serviceData['quantity'] * $serviceData['price'];
                    $proposalTotalOperationalCost = $proposalTotalHours / 3600 * $serviceModel->labor_hourly_rate;

                    // services data
                    $proposalItems[] = new ProposalItem([
                        'service_id' => $serviceModel->id,
                        'account_id' => $serviceModel->account_id,
                        'name' => $serviceModel->name,
                        'quantity' => $serviceData['quantity'],
                        'price' => $serviceData['price'],
                        'profit' => $serviceModel->profit,
                        'profit_percentage' => $serviceModel->profit_percentage,
                        'labor_hours' => $serviceModel->labor_hours,
                        'labor_hours_total' => $serviceData['quantity'] * $serviceModel->labor_hours,
                        'labor_hourly_rate' => $serviceModel->labor_hourly_rate,
                        'labor_hourly_rate_total' => $serviceData['quantity'] * $serviceModel->labor_hourly_rate,
                        'total_price' => $serviceData['quantity'] * $serviceData['price'],
                        'total_profit' => $serviceData['quantity'] * $serviceModel->profit,
                    ]);
                }
            }
            $proposal->total_hours = $proposalTotalHours;
            $proposal->total_third_party_cost = $proposalThirdPartyCost;
            $proposal->total_operational_cost = $proposalTotalOperationalCost;
            $proposal->total_profit = $proposal->total_price - $proposal->total_operational_cost - $proposal->total_third_party_cost;
            $proposal->total_discount = $proposalTotalDiscount;
            $proposal->total_price = $propoposalTotalPrice - $proposalTotalDiscount;
            $proposal->total_profit = ($proposal->total_profit / $proposal->total_price) * 100;
            $proposal->save();
            
            foreach ($proposalItems as $proposalItem) {
                $proposalItem->proposal_id = $proposal->id;
                $proposalItem->save();
            }
            return ProposalResource::make($proposal->load('proposalItems'));
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "Erro ao criar proposta",
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */
    public function show(Proposal $proposal)
    {
        $proposal = Proposal::with([
            'proposalItems',
            'opportunity'
        ])
            ->find($proposal->id);

        if ($proposal) {
            return ProposalResource::make($proposal);
        }
        return response()->json([
            'message' => 'Proposta não encontrada',
        ], 404);
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proposal $proposal)
    {
        foreach ($proposal->services as $service) {
            $proposal->services()->updateExistingPivot($service->id, ['deleted_at' => now()]);
        }
        $proposal->delete();

        return response()->json([
            'message' => "Proposta excluída com sucesso",
        ], 200);
    }


    /**
     * Display a listing of the resource by proposal_id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProposalsByOpportunityId(Request  $request)
    {
        $perPage = request()->get('per_page', 20);

        $proposals = Proposal::where('opportunity_id', $request->opportunity_id)
            ->with('proposalItems')
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        // $proposals->appends(['opportunity_id' => $request->opportunity_id]);

        return ProposalResource::collection($proposals);
    }
}
