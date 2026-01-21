<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\Cost;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\ProposalCost;
use App\Models\ProposalService;
use App\Http\Resources\ProposalsResource;
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

        return ProposalsResource::collection($proposals);
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

            return ProposalsResource::make($proposal->load([
                'proposalServices',
                'proposalCosts',
                'opportunity',
                'opportunity.company',
                'opportunity.lead',
            ]));
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
            'invoices',
            'proposalServices',
            'proposalCosts',
            'opportunity'
        ])
            ->find($proposal->id);

        if ($proposal) {
            return ProposalsResource::make($proposal);
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
                $this->updateProposalStatus($proposal, $validatedData['status']);
            }

            // Se está atualizando apenas total_profit_percentage manualmente
            if (isset($validatedData['total_profit_percentage']) && 
                !$request->has('proposalServices') && 
                !$request->has('proposalCosts')) {
                
                $this->recalculateProfitFromPercentage($proposal, $validatedData['total_profit_percentage']);
                
                $proposal->load([
                    'invoices',
                    'proposalServices',
                    'proposalCosts',
                ]);
                
                return ProposalsResource::make($proposal);
            }

            // Se está atualizando apenas total_profit (valor absoluto) manualmente
            if (isset($validatedData['total_profit']) && 
                !$request->has('proposalServices') && 
                !$request->has('proposalCosts') &&
                !isset($validatedData['total_profit_percentage'])) {
                
                $this->recalculatePriceFromProfit($proposal, $validatedData['total_profit']);
                
                $proposal->load([
                    'invoices',
                    'proposalServices',
                    'proposalCosts',
                ]);
                
                return ProposalsResource::make($proposal);
            }

            $proposal->fill($validatedData);
            $proposal->save();

            // Atualiza ou cria os serviços relacionados à proposta
            if ($request->has('proposalServices')) {
                $this->updateProposalServices($proposal, $request->input('proposalServices', []));
                $this->calculateProposalTotals($proposal);
            }

            // Atualiza os custos e recalcula preço mantendo a margem de lucro
            if ($request->has('proposalCosts')) {
                // Guarda a margem de lucro atual antes de atualizar
                $currentProfitPercentage = $proposal->total_profit_percentage;
                
                $this->updateProposalCosts($proposal, $request->input('proposalCosts', []));
                $this->updateTotalThirdPartyCost($proposal);
                
                // Recalcula o preço mantendo a margem de lucro (reutiliza o método existente)
                $this->recalculateProfitFromPercentage($proposal, $currentProfitPercentage);
            }

            $proposal->load([
                'invoices',
                'proposalServices',
                'proposalCosts',
            ]);

            return ProposalsResource::make($proposal);
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
     * Calculate and update the total values for a proposal.
     *
     * @param  Proposal  $proposal
     * @return void
     */
    private function calculateProposalTotals(Proposal $proposal)
    {
        $proposal->load(['proposalServices', 'proposalCosts']);

        $proposalTotalHours = 0;
        $proposalTotalPrice = 0;
        $proposalTotalProfit = 0;
        $proposalTotalOperationalCost = 0;
        $proposalTotalDiscount = 0;

        foreach ($proposal->proposalServices as $proposalService) {
            $proposalTotalHours += $proposalService->labor_hours_total;
            $proposalTotalPrice += $proposalService->total_price;
            $proposalTotalProfit += $proposalService->total_profit;
            $proposalTotalOperationalCost += $proposalService->labor_hourly_rate_total;
        }
        
        $proposalTotalCosts = 0;
        
        foreach ($proposal->proposalCosts as $proposalCost) {
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
     * Sum and Update the proposal total_third_party_cost.
     *
     * @param  Proposal  $proposal
     * @return ProposalService[] Array of ProposalService objects
     */
    private function updateTotalThirdPartyCost(Proposal $proposal)
    {
        $totalThirdPartyCost = $proposal->proposalCosts->sum('total_price');
        $proposal->total_third_party_cost = $totalThirdPartyCost;
        $proposal->save();
    }

    private function updateOrCreateProposalCostsObjects($costsFormData, $proposalId)
    {
        $proposalCosts = [];
        $totalThirdPartyCostDifference = 0;
        foreach ($costsFormData as $costFormData) {
            $costModel = Cost::find($costFormData['id']);

            if ($costModel) {
                $proposalCost = ProposalCost::updateOrCreate(
                    [
                        'cost_id' => $costModel->id,
                        'proposal_id' => $proposalId
                    ],
                    [
                        'cost_id' => $costModel->id,
                        'account_id' => $costModel->account_id,
                        'name' => $costModel->name,
                        'quantity' => $costFormData['quantity'],
                        'price' => $costModel->price,
                        'total_price' => $costFormData['quantity'] * $costModel->price,
                    ]
                );

                $proposalCosts[] = $proposalCost;
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
                'opportunity',
                'opportunity.company',
                'opportunity.lead',
                'proposalServices',
                'proposalCosts',
            ])
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        // $proposals->appends(['opportunity_id' => $request->opportunity_id]);

        return ProposalsResource::collection($proposals);
    }

    /**
     * Get the total value of all proposals.
     *
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
        $totalValue = Proposal::getTotalValue();
        $acceptedProposalsCount = Proposal::getAcceptedCount();
        return response()->json([
            'total' => $totalValue,
            'acceptedProposalsCount' => $acceptedProposalsCount
        ]);
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

    /**
     * Recalculate profit and price based on new profit percentage.
     *
     * @param  Proposal  $proposal
     * @param  float  $newProfitPercentage
     * @return void
     */
    private function recalculateProfitFromPercentage(Proposal $proposal, $newProfitPercentage)
    {
        $proposal->load(['proposalServices', 'proposalCosts']);
        
        $operationalCost = $proposal->total_operational_cost;
        $thirdPartyCost = $proposal->total_third_party_cost;
        
        // Fórmula: Preço = (Custo Operacional + Custo Terceiros) / (1 - Margem/100)
        $totalCost = $operationalCost + $thirdPartyCost;
        $newTotalPrice = $totalCost / (1 - ($newProfitPercentage / 100));
        $newTotalProfit = $newTotalPrice - $totalCost;
        
        $proposal->total_profit_percentage = $newProfitPercentage;
        $proposal->total_profit = $newTotalProfit;
        $proposal->total_price = $newTotalPrice;
        $proposal->save();
    }

    /**
     * Recalculate price based on new profit value.
     *
     * @param  Proposal  $proposal
     * @param  float  $newProfit
     * @return void
     */
    private function recalculatePriceFromProfit(Proposal $proposal, $newProfit)
    {
        $proposal->load(['proposalServices', 'proposalCosts']);
        
        $operationalCost = $proposal->total_operational_cost;
        $thirdPartyCost = $proposal->total_third_party_cost;
        
        // Fórmula: Preço = Custo Total + Novo Lucro
        $totalCost = $operationalCost + $thirdPartyCost;
        $newTotalPrice = $totalCost + $newProfit;
        $newProfitPercentage = ($newProfit / $newTotalPrice) * 100;
        
        $proposal->total_profit_percentage = $newProfitPercentage;
        $proposal->total_profit = $newProfit;
        $proposal->total_price = $newTotalPrice;
        $proposal->save();
    }

    /**
     * Update the status of the proposal.
     *
     * @param  Proposal  $proposal
     * @param  string  $status
     * @return void
     */
    private function updateProposalStatus(Proposal $proposal, $status)
    {
        $proposal->status = $status;
        $statusColumn = $status . '_at';
        $proposal->$statusColumn = now();
        $proposal->save();
    }

    /**
     * Update the services of the proposal.
     *
     * @param  Proposal  $proposal
     * @param  array  $servicesFormData
     * @return void
     */
    private function updateProposalServices(Proposal $proposal, array $servicesFormData)
    {
        foreach ($servicesFormData as $serviceData) {
            $proposalService = ProposalService::where('proposal_id', $proposal->id)
                ->where('service_id', $serviceData['service_id'])
                ->first();

            if (!$proposalService) {
                // Se o registro não existir, crie um novo
                $proposalService = new ProposalService([
                    'proposal_id' => $proposal->id,
                    'service_id' => $serviceData['service_id'],
                ]);
            }

            // Atualiza campos básicos
            $proposalService->quantity = $serviceData['quantity'] ?? $proposalService->quantity;
            $proposalService->labor_hours = $serviceData['labor_hours'] ?? $proposalService->labor_hours;
            $proposalService->labor_hourly_rate = $serviceData['labor_hourly_rate'] ?? $proposalService->labor_hourly_rate;

            // Recalcula labor_hours_total e labor_hourly_rate_total
            $proposalService->labor_hours_total = $proposalService->quantity * $proposalService->labor_hours;
            $proposalService->labor_hourly_rate_total = $proposalService->quantity * ($proposalService->labor_hourly_rate * ($proposalService->labor_hours / 3600));
            
            // Calcula o custo operacional unitário (base para cálculos de lucro)
            $laborHourlyTotal = $proposalService->labor_hourly_rate * ($proposalService->labor_hours / 3600);

            // Se labor_hourly_rate foi alterado, recalcula price, profit e profit_percentage mantendo o lucro unitário atual (se existir)
            if (isset($serviceData['labor_hourly_rate'])) {
                // Recalcula price como custo operacional + lucro unitário atual
                $currentUnitProfit = $proposalService->profit ?? 0;
                $proposalService->price = $laborHourlyTotal + $currentUnitProfit;
                // Recalcula a porcentagem de lucro
                if ($proposalService->price > 0) {
                    $proposalService->profit_percentage = ($currentUnitProfit / $proposalService->price) * 100;
                } else {
                    $proposalService->profit_percentage = 0;
                }
            }

            // Se profit_percentage foi alterado, recalcula profit e price
            if (isset($serviceData['profit_percentage'])) {
                $proposalService->profit_percentage = $serviceData['profit_percentage'];
                
                // Fórmula: price = laborHourlyTotal / (1 - profit_percentage/100)
                // profit = price - laborHourlyTotal
                if ($proposalService->profit_percentage > 0 && $proposalService->profit_percentage < 100) {
                    $proposalService->price = $laborHourlyTotal / (1 - ($proposalService->profit_percentage / 100));
                    $proposalService->profit = $proposalService->price - $laborHourlyTotal;
                } else if ($proposalService->profit_percentage == 0) {
                    $proposalService->price = $laborHourlyTotal;
                    $proposalService->profit = 0;
                } else {
                    // Se a margem for inválida (>= 100%), mantém valores atuais
                    $proposalService->price = $serviceData['price'] ?? $proposalService->price;
                    $proposalService->profit = $serviceData['profit'] ?? $proposalService->profit;
                }
            }
            // Se profit foi alterado, recalcula profit_percentage e price
            else if (isset($serviceData['profit'])) {
                $proposalService->profit = $serviceData['profit'];
                $proposalService->price = $laborHourlyTotal + $proposalService->profit;
                
                // Recalcula a porcentagem de lucro
                if ($proposalService->price > 0) {
                    $proposalService->profit_percentage = ($proposalService->profit / $proposalService->price) * 100;
                } else {
                    $proposalService->profit_percentage = 0;
                }
            }
            // Se total_profit foi alterado, recalcula profit (unitário), price e profit_percentage
            else if (isset($serviceData['total_profit'])) {
                // Evita divisão por zero: se quantity for 0, considera 1 apenas para cálculo unitário
                $quantity = $proposalService->quantity ?: 1;
                $proposalService->profit = $quantity > 0 ? ($serviceData['total_profit'] / $quantity) : 0;
                $proposalService->price = $laborHourlyTotal + $proposalService->profit;

                // Recalcula a porcentagem de lucro
                if ($proposalService->price > 0) {
                    $proposalService->profit_percentage = ($proposalService->profit / $proposalService->price) * 100;
                } else {
                    $proposalService->profit_percentage = 0;
                }
            }
            // Se price foi alterado diretamente, recalcula profit e profit_percentage
            else if (isset($serviceData['price'])) {
                $proposalService->price = $serviceData['price'];
                $proposalService->profit = $proposalService->price - $laborHourlyTotal;
                
                // Recalcula a porcentagem de lucro
                if ($proposalService->price > 0) {
                    $proposalService->profit_percentage = ($proposalService->profit / $proposalService->price) * 100;
                } else {
                    $proposalService->profit_percentage = 0;
                }
            }
            // Se nenhum dos campos de lucro/preço foi alterado, mantém valores atuais
            else {
                $proposalService->price = $serviceData['price'] ?? $proposalService->price;
                $proposalService->profit = $serviceData['profit'] ?? $proposalService->profit;
                $proposalService->profit_percentage = $serviceData['profit_percentage'] ?? $proposalService->profit_percentage;
            }

            // Recalcula totais
            $proposalService->total_price = $proposalService->quantity * $proposalService->price;
            $proposalService->total_profit = $proposalService->quantity * $proposalService->profit;

            // Salva o registro atualizado
            $proposalService->save();
        }
    }

    /**
     * Update the costs of the proposal.
     *
     * @param  Proposal  $proposal
     * @param  array  $costsFormData
     * @return void
     */
    private function updateProposalCosts(Proposal $proposal, array $costsFormData)
    {
        foreach ($costsFormData as $costData) {
    
            // Busca o registro de custo correspondente
            $proposalCost = ProposalCost::where('proposal_id', $proposal->id)
                ->where('cost_id', $costData['cost_id'])
                ->first();

            // Se não existir, cria um novo
            if (!$proposalCost) {
                $proposalCost = new ProposalCost([
                    'proposal_id' => $proposal->id,
                    'cost_id' => $costData['cost_id'],
                ]);
            }
    
            // Atualiza campos simples
            $proposalCost->quantity = $costData['quantity'] ?? $proposalCost->quantity;
            $proposalCost->price = $costData['price'] ?? $proposalCost->price;
    
            // Recalcula total
            if ($proposalCost->quantity !== null && $proposalCost->price !== null) {
                $proposalCost->total_price = $proposalCost->quantity * $proposalCost->price;
            }
    
            $proposalCost->description = $costData['description'] ?? $proposalCost->description;
    
            // Salva o registro atualizado
            $proposalCost->save();
        }
    }
}
