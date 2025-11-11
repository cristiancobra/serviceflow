<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankAccountRequest;
use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = BankAccount::with(['account', 'user'])
            ->withCount('transactions');

        // Filtro por conta
        if ($request->has('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // Filtro por usuário
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por tipo
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por status ativo
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Busca por nome
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('agency', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $bankAccounts = $query->paginate($perPage);

        return BankAccountResource::collection($bankAccounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BankAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BankAccountRequest $request)
    {
        $data = $request->validated();
        
        // Define saldo inicial como 0 se não informado
        if (!isset($data['initial_balance'])) {
            $data['initial_balance'] = 0;
        }

        // Define is_active como true se não informado
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        $bankAccount = BankAccount::create($data);
        $bankAccount->load(['account', 'user']);

        return new BankAccountResource($bankAccount);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load(['account', 'user'])
            ->loadCount('transactions');

        return new BankAccountResource($bankAccount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BankAccountRequest  $request
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(BankAccountRequest $request, BankAccount $bankAccount)
    {
        $data = $request->validated();
        
        $bankAccount->update($data);
        $bankAccount->load(['account', 'user']);

        return new BankAccountResource($bankAccount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        // Verifica se há transações vinculadas
        if ($bankAccount->transactions()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível excluir esta conta bancária pois existem transações vinculadas a ela.'
            ], 422);
        }

        $bankAccount->delete();

        return response()->json([
            'message' => 'Conta bancária excluída com sucesso.'
        ]);
    }

    /**
     * Restore a soft deleted bank account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $bankAccount = BankAccount::withTrashed()->findOrFail($id);
        $bankAccount->restore();
        $bankAccount->load(['account', 'user']);

        return new BankAccountResource($bankAccount);
    }

    /**
     * Force delete a bank account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        $bankAccount = BankAccount::withTrashed()->findOrFail($id);
        
        // Verifica se há transações vinculadas
        if ($bankAccount->transactions()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível excluir permanentemente esta conta bancária pois existem transações vinculadas a ela.'
            ], 422);
        }

        $bankAccount->forceDelete();

        return response()->json([
            'message' => 'Conta bancária excluída permanentemente com sucesso.'
        ]);
    }

    /**
     * Get account type options.
     *
     * @return \Illuminate\Http\Response
     */
    public function typeOptions()
    {
        return response()->json(BankAccount::getTypeOptions());
    }

    /**
     * Update bank account balance based on transactions.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function updateBalance(BankAccount $bankAccount)
    {
        $newBalance = $bankAccount->updateBalance();

        return response()->json([
            'message' => 'Saldo atualizado com sucesso.',
            'balance' => $newBalance,
            'balance_formatted' => 'R$ ' . number_format($newBalance, 2, ',', '.')
        ]);
    }

    /**
     * Toggle bank account active status.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(BankAccount $bankAccount)
    {
        $bankAccount->update(['is_active' => !$bankAccount->is_active]);
        $bankAccount->load(['account', 'user']);

        return new BankAccountResource($bankAccount);
    }
}
