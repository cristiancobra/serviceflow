<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecurringExpenseRequest;
use App\Http\Resources\RecurringExpenseResource;
use App\Models\RecurringExpense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecurringExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = RecurringExpense::with(['user', 'department'])
            ->withCount('invoices');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $query->orderBy('due_day');

        $perPage = $request->get('per_page', 500);
        $recurringExpenses = $query->paginate($perPage);

        return RecurringExpenseResource::collection($recurringExpenses);
    }

    public function store(RecurringExpenseRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        if (!isset($data['category'])) {
            $data['category'] = RecurringExpense::CATEGORY_FIXED;
        }

        $recurringExpense = RecurringExpense::create($data);
        $recurringExpense->load(['user', 'department']);

        return new RecurringExpenseResource($recurringExpense);
    }

    public function show(RecurringExpense $recurringExpense)
    {
        $recurringExpense->load([
            'user',
            'department',
            'invoices' => function ($query) {
                $query->orderBy('date_due', 'desc');
            },
        ])->loadCount('invoices');

        return new RecurringExpenseResource($recurringExpense);
    }

    public function update(RecurringExpenseRequest $request, RecurringExpense $recurringExpense)
    {
        $data = $request->validated();

        $recurringExpense->update($data);
        $recurringExpense->load(['user', 'department']);

        return new RecurringExpenseResource($recurringExpense);
    }

    public function destroy(RecurringExpense $recurringExpense)
    {
        $recurringExpense->delete();

        return response()->json([
            'message' => 'Despesa recorrente excluída com sucesso.',
        ]);
    }

    public function toggleActive(RecurringExpense $recurringExpense)
    {
        $recurringExpense->update(['is_active' => !$recurringExpense->is_active]);
        $recurringExpense->load(['user', 'department']);

        return new RecurringExpenseResource($recurringExpense);
    }

    /**
     * Gera retroativamente as faturas desde o start_date da despesa recorrente até
     * hoje (ou até $request->end_date, se informado). Idempotente: faturas já
     * existentes para uma data de vencimento não são duplicadas.
     */
    public function backfill(Request $request, RecurringExpense $recurringExpense)
    {
        $validated = $request->validate([
            'end_date' => 'nullable|date',
        ]);

        $endDate = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])
            : Carbon::today();

        $generated = 0;

        foreach ($recurringExpense->dueDatesBetween($recurringExpense->start_date, $endDate) as $dueDate) {
            $invoice = $recurringExpense->generateInvoiceForDate($dueDate);

            if ($invoice->wasRecentlyCreated) {
                $generated++;
            }
        }

        return response()->json([
            'message' => $generated > 0
                ? "{$generated} fatura(s) gerada(s) com sucesso."
                : 'Nenhuma fatura nova para gerar no período.',
            'generated' => $generated,
        ]);
    }
}
