<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditCardRequest;
use App\Http\Resources\CreditCardResource;
use App\Models\CreditCard;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditCard::with(['user', 'defaultBankAccount'])
            ->withCount('invoices');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('last_digits', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $creditCards = $query->paginate($perPage);

        return CreditCardResource::collection($creditCards);
    }

    public function store(CreditCardRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        $creditCard = CreditCard::create($data);
        $creditCard->load(['user', 'defaultBankAccount']);

        return new CreditCardResource($creditCard);
    }

    public function show(CreditCard $creditCard)
    {
        $creditCard->load(['user', 'defaultBankAccount'])
            ->loadCount('invoices');

        return new CreditCardResource($creditCard);
    }

    public function update(CreditCardRequest $request, CreditCard $creditCard)
    {
        $data = $request->validated();

        $creditCard->update($data);
        $creditCard->load(['user', 'defaultBankAccount']);

        return new CreditCardResource($creditCard);
    }

    public function destroy(CreditCard $creditCard)
    {
        if ($creditCard->charges()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível excluir este cartão pois existem compras lançadas nele.'
            ], 422);
        }

        $creditCard->delete();

        return response()->json([
            'message' => 'Cartão de crédito excluído com sucesso.'
        ]);
    }

    public function restore($id)
    {
        $creditCard = CreditCard::withTrashed()->findOrFail($id);
        $creditCard->restore();
        $creditCard->load(['user', 'defaultBankAccount']);

        return new CreditCardResource($creditCard);
    }

    public function forceDelete($id)
    {
        $creditCard = CreditCard::withTrashed()->findOrFail($id);

        if ($creditCard->charges()->count() > 0) {
            return response()->json([
                'message' => 'Não é possível excluir permanentemente este cartão pois existem compras lançadas nele.'
            ], 422);
        }

        $creditCard->forceDelete();

        return response()->json([
            'message' => 'Cartão de crédito excluído permanentemente com sucesso.'
        ]);
    }

    public function toggleActive(CreditCard $creditCard)
    {
        $creditCard->update(['is_active' => !$creditCard->is_active]);
        $creditCard->load(['user', 'defaultBankAccount']);

        return new CreditCardResource($creditCard);
    }
}
