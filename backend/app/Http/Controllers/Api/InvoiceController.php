<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Http\Resources\InvoicesResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::with([
            'proposal',
            'proposal.opportunity',
            'proposal.opportunity.company',
            'proposal.opportunity.lead',
            'transactions',
        ])
            ->orderBy('date_due', 'desc')
            ->paginate(500);

        return InvoicesResource::collection($invoices);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Verifica se é uma fatura de débito individual (com price) ou múltiplas faturas (com prices)
            if (isset($validated['price']) && !isset($validated['prices'])) {
                // Fatura de débito individual
                $invoiceData = [
                    'proposal_id' => $validated['proposal_id'],
                    'user_id' => $validated['user_id'],
                    'lead_id' => $validated['lead_id'] ?? null,
                    'price' => $validated['price'],
                    'balance' => $validated['price'],
                    'date_due' => $validated['date_due'],
                    'type' => $validated['type'] ?? 'debit',
                    'category' => $validated['category'] ?? null,
                    'observations' => $validated['observations'] ?? null,
                ];
                
                $invoice = Invoice::create($invoiceData);
                return InvoicesResource::collection([$invoice]);
            }
            
            // Verifica se existem múltiplas faturas (parceladas)
            if (!isset($validated['prices'])) {
                return response()->json([
                    'message' => "Dados inválidos. Envie 'price' para fatura única ou 'prices' para parceladas",
                    'errors' => ['prices' => ['Campo prices ou price é obrigatório']],
                ], 422);
            }
            
            // Faturas parceladas (lógica existente)
            $prices = $validated['prices'];
            $dateDue = $validated['date_due'];
            unset($validated['prices'], $validated['date_due']);
        
            $invoices = [];
            foreach ($prices as $index => $price) {
                $invoiceData = array_merge($validated, [
                    'price' => $price,
                    'balance' => $price,
                    'date_due' => date('Y-m-d', strtotime("+$index month", strtotime($dateDue))),
                    'type' => 'credit',
                ]);
                $invoice = new Invoice;
                $invoice->fill($invoiceData);
                $invoice->save();
                $invoices[] = $invoice;
            }

            return InvoicesResource::collection($invoices);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Store a debit invoice (individual).
     *
     * @param  InvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDebit(InvoiceRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Fatura de débito individual
            $invoiceData = [
                'proposal_id' => $validated['proposal_id'],
                'user_id' => $validated['user_id'],
                'price' => $validated['price'],
                'balance' => $validated['price'],
                'date_due' => $validated['date_due'],
                'type' => 'debit',
                'observations' => $validated['observations'] ?? null,
            ];
            
            $invoice = Invoice::create($invoiceData);
            return InvoicesResource::collection([$invoice]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar fatura de débito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store credit invoices (installments).
     *
     * @param  InvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCredit(InvoiceRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Verifica se existem múltiplas faturas (parceladas)
            if (!isset($validated['prices'])) {
                return response()->json([
                    'message' => "Dados inválidos. Envie 'prices' (array) para faturas parceladas",
                    'errors' => ['prices' => ['Campo prices é obrigatório para faturas de crédito']],
                ], 422);
            }
            
            // Faturas parceladas (lógica existente)
            $prices = $validated['prices'];
            $dateDue = $validated['date_due'];
            unset($validated['prices'], $validated['date_due']);
        
            $invoices = [];
            foreach ($prices as $index => $price) {
                $invoiceData = array_merge($validated, [
                    'price' => $price,
                    'balance' => $price,
                    'date_due' => date('Y-m-d', strtotime("+$index month", strtotime($dateDue))),
                    'type' => 'credit',
                ]);
                $invoice = new Invoice;
                $invoice->fill($invoiceData);
                $invoice->save();
                $invoices[] = $invoice;
            }

            return InvoicesResource::collection($invoices);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar faturas de crédito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        if($invoice) {
            return InvoicesResource::make($invoice->load([
                'proposal.opportunity',
                'proposal.opportunity.company',
                'proposal.opportunity.lead',
                'user',
                'transactions'
            ]));
        }
        return response()->json([
            'message' => 'Fatura não encontrada',
        ], 404);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  InvoiceRequest  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        try {
            $validatedData = $request->validated();

            // Se o preço está sendo atualizado
            if (isset($validatedData['price'])) {
                // Apenas valida e ajusta faturas de CRÉDITO
                // Faturas de DÉBITO podem ter qualquer valor (custos reais, até prejuízo)
                if ($invoice->type === 'credit') {
                    $validationError = $this->validatePriceUpdate($invoice, $validatedData['price']);
                    if ($validationError) {
                        return response()->json([
                            'message' => 'Erro ao atualizar o valor da fatura',
                            'errors' => ['price' => [$validationError]]
                        ], 422);
                    }
                    $this->adjustInvoicePrices($invoice, $validatedData['price']);
                } else {
                    // Para faturas de débito, atualiza diretamente sem validação de limite
                    $invoice->update($validatedData);
                    // Recalcula o balance considerando as transações
                    $totalTransactions = $invoice->transactions()->sum('amount');
                    $invoice->balance = $validatedData['price'] - $totalTransactions;
                    $invoice->save();
                }
            } else {
                // Atualiza apenas os campos enviados na requisição (exceto preço)
                $invoice->update($validatedData);
            }

            // Atualiza o status da invoice após qualquer alteração
            $invoice->updateStatus();

            return InvoicesResource::make($invoice->load([
                'proposal.opportunity',
                'proposal.opportunity.company', 
                'proposal.opportunity.lead',
                'user',
                'transactions'
            ]));

        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar fatura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valida se o novo preço resultaria em valores negativos
     * APENAS PARA FATURAS DE CRÉDITO
     *
     * @param Invoice $changedInvoice
     * @param float $newPrice
     * @return string|null Retorna mensagem de erro ou null se válido
     */
    private function validatePriceUpdate(Invoice $changedInvoice, float $newPrice)
    {
        $proposal = $changedInvoice->proposal;
        $totalPrice = $proposal->total_price;

        // Busca todas as faturas de CRÉDITO da proposta ordenadas por data de vencimento
        $allInvoices = Invoice::where('proposal_id', $proposal->id)
            ->where('type', 'credit')
            ->orderBy('date_due')
            ->get();

        // Se há apenas uma fatura de crédito, não precisa validar redistribuição
        if ($allInvoices->count() === 1) {
            return null;
        }

        // Encontra o índice da fatura que está sendo alterada
        $changedIndex = $allInvoices->search(function ($invoice) use ($changedInvoice) {
            return $invoice->id === $changedInvoice->id;
        });

        if ($changedIndex === false) {
            return 'Fatura não encontrada na proposta';
        }

        // Calcula a soma das faturas anteriores à fatura alterada
        $sumBefore = $allInvoices->slice(0, $changedIndex)->sum('price');
        $remaining = $totalPrice - $sumBefore;

        // Verifica se o novo valor resultaria em valor negativo
        if ($newPrice > $remaining) {
            $sumBeforeFormatted = number_format($sumBefore, 2, ',', '.');
            $totalPriceFormatted = number_format($totalPrice, 2, ',', '.');
            $remainingFormatted = number_format($remaining, 2, ',', '.');
            
            return "O valor informado (R$ " . number_format($newPrice, 2, ',', '.') . ") excede o valor disponível (R$ {$remainingFormatted}). " .
                   "Total da proposta: R$ {$totalPriceFormatted}. " .
                   "Já alocado em parcelas anteriores: R$ {$sumBeforeFormatted}.";
        }

        // Calcula o valor restante após a alteração para as próximas faturas
        $remainingAfterChange = $totalPrice - $sumBefore - $newPrice;
        $remainingInvoices = $allInvoices->slice($changedIndex + 1);
        $remainingInvoicesCount = $remainingInvoices->count();

        // Se há faturas posteriores, verifica se o valor restante resultaria em negativo
        if ($remainingInvoicesCount > 0 && $remainingAfterChange < 0) {
            return "Não é possível atribuir este valor pois resultaria em valores negativos nas parcelas subsequentes.";
        }

        return null;
    }

    /**
     * Ajusta os preços das faturas subsequentes quando uma fatura DE CRÉDITO é atualizada
     *
     * @param Invoice $changedInvoice
     * @param float $newPrice
     * @return void
     */
    private function adjustInvoicePrices(Invoice $changedInvoice, float $newPrice)
    {
        Log::info('adjustInvoicePrices called', [
            'invoice_id' => $changedInvoice->id,
            'new_price' => $newPrice,
            'old_price' => $changedInvoice->price,
            'type' => $changedInvoice->type
        ]);

        // Busca a proposta para obter o valor total
        $proposal = $changedInvoice->proposal;
        $totalPrice = $proposal->total_price;

        Log::info('Proposal info', [
            'proposal_id' => $proposal->id,
            'total_price' => $totalPrice
        ]);

        // Busca todas as faturas DE CRÉDITO da proposta ordenadas por data de vencimento
        $allInvoices = Invoice::where('proposal_id', $proposal->id)
            ->where('type', 'credit')
            ->orderBy('date_due')
            ->get();

        Log::info('All credit invoices', [
            'count' => $allInvoices->count(),
            'invoices' => $allInvoices->map(fn($i) => ['id' => $i->id, 'price' => $i->price, 'type' => $i->type])
        ]);

        // Se há apenas uma fatura de crédito, atualiza diretamente sem redistribuir
        if ($allInvoices->count() === 1) {
            Log::info('Only one credit invoice, updating directly');
            $changedInvoice->update(['price' => $newPrice, 'balance' => $newPrice]);
            return;
        }

        // Encontra o índice da fatura que está sendo alterada
        $changedIndex = $allInvoices->search(function ($invoice) use ($changedInvoice) {
            return $invoice->id === $changedInvoice->id;
        });

        if ($changedIndex === false) {
            throw new \Exception('Fatura não encontrada na proposta');
        }

        // Calcula a soma das faturas anteriores à fatura alterada
        $sumBefore = $allInvoices->slice(0, $changedIndex)->sum('price');
        $remaining = $totalPrice - $sumBefore;

        Log::info('Calculations', [
            'changed_index' => $changedIndex,
            'sum_before' => $sumBefore,
            'remaining' => $remaining
        ]);

        // Verifica se o novo valor não excede o valor restante
        if ($newPrice > $remaining) {
            Log::warning('New price exceeds remaining, adjusting', [
                'new_price' => $newPrice,
                'remaining' => $remaining
            ]);
            $newPrice = $remaining;
        }

        // Atualiza a fatura alterada
        $changedInvoice->update(['price' => $newPrice, 'balance' => $newPrice]);

        // Calcula o valor restante após a alteração
        $remainingAfterChange = $totalPrice - $sumBefore - $newPrice;
        $remainingInvoices = $allInvoices->slice($changedIndex + 1);
        $remainingInvoicesCount = $remainingInvoices->count();

        Log::info('Adjusting remaining invoices', [
            'remaining_after_change' => $remainingAfterChange,
            'remaining_invoices_count' => $remainingInvoicesCount
        ]);

        if ($remainingInvoicesCount > 0) {
            // Calcula o novo valor por fatura restante
            $newPricePerInvoice = round($remainingAfterChange / $remainingInvoicesCount, 2);

            // Atualiza todas as faturas seguintes com o valor calculado
            foreach ($remainingInvoices as $index => $invoice) {
                $invoice->update(['price' => $newPricePerInvoice, 'balance' => $newPricePerInvoice]);
            }

            // Ajusta a última fatura para compensar diferenças de arredondamento
            $totalCalculated = $sumBefore + $newPrice + ($newPricePerInvoice * $remainingInvoicesCount);
            $difference = $totalPrice - $totalCalculated;
            
            if ($difference != 0 && $remainingInvoicesCount > 0) {
                $lastInvoice = $remainingInvoices->last();
                $lastInvoice->update(['price' => $newPricePerInvoice + $difference, 'balance' => $newPricePerInvoice + $difference]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
