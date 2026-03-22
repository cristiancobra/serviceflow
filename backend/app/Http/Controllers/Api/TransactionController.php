<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Http\Resources\TransactionsResource;
use Illuminate\Http\Request;
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

            // Atualiza o total_paid e status da invoice
            if ($transaction->invoice) {
                $transaction->invoice->updateTotalPaid();
                $transaction->invoice->updateStatus();
            }

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
     * @param  TransactionRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionRequest $request, $id)
    {
        $validated = $request->validated();
        
        $transaction = Transaction::findOrFail($id);

        $transaction->fill($validated);
        $transaction->save();

        // Atualiza o total_paid e status da invoice
        if ($transaction->invoice) {
            $transaction->invoice->updateTotalPaid();
            $transaction->invoice->updateStatus();
        }

        return TransactionsResource::make($transaction->load('invoice', 'bankAccount'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            
            // O soft delete vai disparar o evento 'deleted' no modelo
            // que automaticamente atualizará o total_paid e status da invoice
            $transaction->delete();
            
            return response()->json([
                'message' => 'Transação excluída com sucesso',
                'data' => null
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir transação',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get report of transactions by year
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $year = $request->query('year', date('Y'));

        // Busca transações do ano filtrado
        $transactions = Transaction::whereYear('transaction_date', $year)->get();

        // Calcula total de entradas (crédito)
        $totalEntries = $transactions->where('type', 'credit')->sum('amount');

        // Calcula total de saídas (débito)
        $totalExits = $transactions->where('type', 'debit')->sum('amount');

        // Calcula o saldo
        $balance = $totalEntries - $totalExits;

        return response()->json([
            'totalEntries' => $totalEntries,
            'totalExits' => $totalExits,
            'balance' => $balance,
            'year' => $year,
        ]);
    }
}
