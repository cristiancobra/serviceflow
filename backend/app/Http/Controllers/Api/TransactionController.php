<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Http\Resources\TransactionsResource;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::with([
            'invoice.proposal.opportunity.company',
            'invoice.proposal.opportunity.lead',
            'bankAccount'
        ])
            ->orderBy('transaction_date', 'desc')
            ->paginate(500);

        return TransactionsResource::collection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request)
    {
        try {
            $validated = $request->validated();

            $transaction = Transaction::create($validated);

            return TransactionsResource::make($transaction->load('invoice', 'bankAccount'));

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Erro interno do servidor",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
