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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
