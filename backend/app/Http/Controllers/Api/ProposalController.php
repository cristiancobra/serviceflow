<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Cost;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\ProposalCost;
use App\Models\ProposalService;
use App\Http\Resources\ProposalResource;
use App\Http\Requests\ProposalRequest;
use Dompdf\Dompdf;

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
            // proposal services
            $servicesFormData = $request->input('services', []);
            $proposalServices = $this->createProposalServicesObjects($servicesFormData);

            // proposal costs
            $costsFormData = $request->input('costs', []);
            $proposalCosts = $this->createProposalCostsObjects($costsFormData);

            // proposal
            $proposal = new Proposal;
            $proposal->fill($request->all());

            $proposalTotalHours = 0;
            $proposalTotalDiscount = 0;
            $proposalTotalPrice = 0;
            $proposalTotalProfit = 0;
            $proposalTotalOperationalCost = 0;

            foreach ($proposalServices as $proposalService) {
                $proposalTotalHours += $proposalService->labor_hours_total;
                // $proposalTotalDiscount += $proposalService->total_price * $proposalService->profit_percentage / 100;
                $proposalTotalPrice += $proposalService->total_price;
                $proposalTotalProfit += $proposalService->total_profit;
                $proposalTotalOperationalCost += $proposalService->labor_hourly_rate_total;
            }

            $proposalTotalCosts = 0;

            foreach ($proposalCosts as $proposalCost) {
                $proposalTotalCosts += $proposalCost->total_price;
            }

            $proposal->total_hours = $proposalTotalHours;
            $proposal->total_third_party_cost = $proposalTotalCosts;
            $proposal->total_operational_cost = $proposalTotalOperationalCost;
            $proposal->total_profit = $proposalTotalProfit;
            $proposal->total_discount = $proposalTotalDiscount;
            $proposal->total_price = $proposalTotalPrice + $proposalTotalCosts;
            $proposal->total_profit_percentage = ($proposal->total_profit / $proposal->total_price) * 100;

            $proposal->save();

            foreach ($proposalServices as $proposalService) {
                $proposalService->proposal_id = $proposal->id;
                $proposalService->save();
            }

            foreach ($proposalCosts as $proposalCost) {
                $proposalCost->proposal_id = $proposal->id;
                $proposalCost->save();
            }

            return ProposalResource::make($proposal->load('proposalServices'));
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
            'proposalServices',
            'proposalCosts',
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
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProposalRequest  $request
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */
    public function update(ProposalRequest $request, Proposal $proposal)
    {
        try {
            $validatedData = $request->validated();

            // Se o status foi alterado, atualize a coluna correspondente
            if (isset($validatedData['status']) && $validatedData['status'] != $proposal->status) {
                $proposal->status = $validatedData['status'];
                $statusColumn = $validatedData['status'] . '_at';
                $proposal->$statusColumn = now();
            }
    
            $proposal->fill($validatedData);
            $proposal->save();

            return ProposalResource::make($proposal);
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
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proposal $proposal)
    {
        foreach ($proposal->proposalServices as $proposalService) {
            $proposalService->delete();
        }
        $proposal->delete();

        return response()->json([
            'message' => "Proposta excluída com sucesso",
        ], 200);
    }

    /**
     * Calculate the proposal costs.
     *
     * @param  array  $proposalServicesData
     * @return float
     */
    private function calculateProposalCosts($costsFormData)
    {
        $proposalTotalCost = 0;

        foreach ($costsFormData as $proposalCost) {
            $proposalTotalCost += $proposalCost['quantity'] * $proposalCost['price'];
        }

        return $proposalTotalCost;
    }

        /** 
     * Count the number of opportunities with date_conclusion null
     * 
     * @return \Illuminate\Http\Response
     * 
     */
    public function countOpenProposals()
    {
        try {
            $totalProposals = Proposal::whereNull('accepted_at')
            ->whereNull('rejected_at')
            ->whereNull('canceled_at')
            ->count();

            return response()->json(['totalProposals' => $totalProposals]);
        } catch (\Exception $e) {
            \Log::error('Error counting open proposals: ' . $e->getMessage());

            return response()->json(['error' => 'Unable to count open proposals'], 500);
        }
    }


    /**
     * Create the proposal services objects.
     *
     * @param  array  $servicesFormData
     * @return ProposalService[] Array of ProposalService objects
     */
    private function createProposalServicesObjects($servicesFormData)
    {
        $proposalServices = [];
        foreach ($servicesFormData as $serviceFormData) {
            $serviceModel = Service::find($serviceFormData['id']);
            if ($serviceModel) {
                // services data
                $proposalServices[] = new ProposalService([
                    'service_id' => $serviceModel->id,
                    'account_id' => $serviceModel->account_id,
                    'name' => $serviceModel->name,
                    'quantity' => $serviceFormData['quantity'],
                    'price' => $serviceModel->price,
                    'profit' => $serviceModel->profit,
                    'profit_percentage' => $serviceModel->profit_percentage,
                    'labor_hours' => $serviceModel->labor_hours,
                    'labor_hours_total' => $serviceFormData['quantity'] * $serviceModel->labor_hours,
                    'labor_hourly_rate' => $serviceModel->labor_hourly_rate,
                    'labor_hourly_rate_total' => $serviceFormData['quantity'] * $serviceModel->labor_hourly_rate,
                    'total_price' => $serviceFormData['quantity'] * $serviceModel->price,
                    'total_profit' => $serviceFormData['quantity'] * $serviceModel->profit,
                ]);
            }
        }

        return $proposalServices;
    }

    private function createProposalCostsObjects($costsFormData)
    {
        $proposalCosts = [];
        foreach ($costsFormData as $costFormData) {
            $costModel = Cost::find($costFormData['id']);

            if ($costModel) {
                $proposalCosts[] = new ProposalCost([
                    'cost_id' => $costModel->id,
                    'account_id' => $costModel->account_id,
                    'name' => $costModel->name,
                    'quantity' => $costFormData['quantity'],
                    'price' => $costModel->price,
                    'total_price' => $costFormData['quantity'] * $costModel->price,
                ]);
            }
        }
        return $proposalCosts;
    }

    /**
     * Export the specified resource to PDF.
     *
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Proposal $proposal)
    {
        $proposal->load([
            'account',
            'opportunity',
            'proposalServices',
        ]);

        $html = view('proposals.proposal', ['proposal' => $proposal])->render();
        $dompdf = new Dompdf();
        // Carregar o HTML no Dompdf
        $dompdf->loadHtml($html);

        // Renderizar o PDF
        $dompdf->render();

        // Enviar o PDF para o navegador
        $dompdf->stream("proposal-{$proposal->id}.pdf");
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
            ->with([
                'proposalServices',
                'proposalCosts',
            ])
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        // $proposals->appends(['opportunity_id' => $request->opportunity_id]);

        return ProposalResource::collection($proposals);
    }
}
