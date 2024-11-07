<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request()->get('per_page', 20);

        $proposals = Proposal::with([
            'proposalServices',
            'proposalCosts',
            'opportunity',
            'opportunity.company',
            'opportunity.lead',
        ])
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        return ProposalResource::collection($proposals);
    }

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
                    'labor_hourly_rate_total' => $serviceFormData['quantity'] * ($serviceModel->labor_hourly_rate  * ($serviceModel->labor_hours / 3600)),
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
     * Configure name for proposal
     */
    public function configureFromName(Proposal $proposal)
    {
        return $proposal->opportunity->company->business_name
            ?? $proposal->opportunity->company->legal_name
            ?? $proposal->opportunity->lead->name
            ?? null;
    }

    /**
     * Convert names to slug
     */
    public function convertToSlug($text)
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = str_replace(' ', '-', $text);
        $text = preg_replace('/[^A-Za-z0-9\-]/', '', $text);
        $text = strtolower($text);
        return $text;
    }


    /**
     * Export the specified resource to PDF.
     *
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Proposal $proposal)
    {
        $isVisibleQuantity = filter_var(request()->query('isVisibleQuantity', false), FILTER_VALIDATE_BOOLEAN);

        $proposal->load([
            'account',
            'opportunity',
            'proposalServices',
        ]);
        $companyName = $this->configureFromName($proposal);
        $slugCompanyName = $this->convertToSlug($companyName);
        $formatter = new \IntlDateFormatter('pt_BR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
        $logo = $this->userImageToBase64($proposal->account->logo);
        $emailIcon = $this->systemImageToBase64('img/proposals/email-icon.png');
        $whatsappIcon = $this->systemImageToBase64('img/proposals/whatsapp-icon.png');
        $today = $formatter->format(new \DateTime);
        $proposalDate = (new \DateTime($proposal->date))->format('d/m/Y');
        $html = view('proposals.proposal', [
            'proposal' => $proposal,
            'logo' => $logo,
            'whatsappIcon' => $whatsappIcon,
            'emailIcon' => $emailIcon,
            'today' => $today,
            'proposalDate' => $proposalDate,
            'isVisibleQuantity' => $isVisibleQuantity,
            'companyName' => $companyName,
        ])
            ->render();


        $dateSuffix = (new \DateTime())->format('d-m-Y');

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("proposta-{$slugCompanyName}-{$dateSuffix}.pdf", ["Attachment" => true]);
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

    /**
     * convert image to base64 from users data
     *
     * @return \Illuminate\Http\Response
     */
    private function userImageToBase64($imagePath)
    {
        $imageCompletePath = public_path('storage/' . $imagePath);
        $type = pathinfo($imageCompletePath, PATHINFO_EXTENSION);
        $data = file_get_contents($imageCompletePath);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }

    /**
     * convert image to base64
     *
     * @return \Illuminate\Http\Response
     */
    private function systemImageToBase64($imagePath)
    {
        $imageCompletePath = public_path($imagePath);
        $type = pathinfo($imageCompletePath, PATHINFO_EXTENSION);
        $data = file_get_contents($imageCompletePath);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }
}
