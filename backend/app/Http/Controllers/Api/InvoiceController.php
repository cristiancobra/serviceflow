<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Http\Resources\InvoicesResource;
use Illuminate\Validation\ValidationException;

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
            $prices = $validated['prices'];
            $dateDue = $validated['date_due'];
            unset($validated['prices'], $validated['date_due']);
    
            $invoices = [];
            foreach ($prices as $index => $price) {
                $invoiceData = array_merge($validated, [
                    'price' => $price,
                    'date_due' => date('Y-m-d', strtotime("+$index month", strtotime($dateDue)))
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

            // Se o preço está sendo atualizado, ajustar as faturas subsequentes
            if (isset($validatedData['price'])) {
                $this->adjustInvoicePrices($invoice, $validatedData['price']);
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
     * Ajusta os preços das faturas subsequentes quando uma fatura é atualizada
     *
     * @param Invoice $changedInvoice
     * @param float $newPrice
     * @return void
     */
    private function adjustInvoicePrices(Invoice $changedInvoice, float $newPrice)
    {
        // Busca a proposta para obter o valor total
        $proposal = $changedInvoice->proposal;
        $totalPrice = $proposal->total_price;

        // Busca todas as faturas da proposta ordenadas por data de vencimento
        $allInvoices = Invoice::where('proposal_id', $proposal->id)
            ->orderBy('date_due')
            ->get();

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

        // Verifica se o novo valor não excede o valor restante
        if ($newPrice > $remaining) {
            $newPrice = $remaining;
        }

        // Atualiza a fatura alterada
        $changedInvoice->update(['price' => $newPrice]);

        // Calcula o valor restante após a alteração
        $remainingAfterChange = $totalPrice - $sumBefore - $newPrice;
        $remainingInvoices = $allInvoices->slice($changedIndex + 1);
        $remainingInvoicesCount = $remainingInvoices->count();

        if ($remainingInvoicesCount > 0) {
            // Calcula o novo valor por fatura restante
            $newPricePerInvoice = round($remainingAfterChange / $remainingInvoicesCount, 2);

            // Atualiza todas as faturas seguintes com o valor calculado
            foreach ($remainingInvoices as $index => $invoice) {
                $invoice->update(['price' => $newPricePerInvoice]);
            }

            // Ajusta a última fatura para compensar diferenças de arredondamento
            $totalCalculated = $sumBefore + $newPrice + ($newPricePerInvoice * $remainingInvoicesCount);
            $difference = $totalPrice - $totalCalculated;
            
            if ($difference != 0 && $remainingInvoicesCount > 0) {
                $lastInvoice = $remainingInvoices->last();
                $lastInvoice->update(['price' => $newPricePerInvoice + $difference]);
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
