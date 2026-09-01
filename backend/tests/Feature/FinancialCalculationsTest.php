<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\Opportunity;
use App\Models\Proposal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Cobre as regras de cálculo financeiro mais sensíveis do sistema (sem cobertura
 * automatizada até aqui): saldo de fatura por transações, saldo de conta bancária,
 * status de fatura e a divisão/redistribuição de parcelas de propostas.
 */
class FinancialCalculationsTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
        $this->user = User::factory()->create(['account_id' => $this->account->id]);

        Sanctum::actingAs($this->user);
    }

    private function createInvoice(array $overrides = []): Invoice
    {
        return Invoice::create(array_merge([
            'user_id' => $this->user->id,
            'date_due' => now()->addDays(10)->toDateString(),
            'price' => 1000,
            'balance' => 1000,
            'type' => 'credit',
        ], $overrides));
    }

    private function createBankAccount(array $overrides = []): BankAccount
    {
        return BankAccount::create(array_merge([
            'user_id' => $this->user->id,
            'account_name' => 'Conta Teste',
            'account_number' => '12345-6',
            'bank_name' => 'Banco Teste',
            'initial_balance' => 0,
        ], $overrides));
    }

    private function createTransaction(Invoice $invoice, BankAccount $bankAccount, string $type, float $amount): Transaction
    {
        return Transaction::create([
            'invoice_id' => $invoice->id,
            'bank_account_id' => $bankAccount->id,
            'amount' => $amount,
            'transaction_date' => now()->toDateString(),
            'type' => $type,
            'method' => 'pix',
        ]);
    }

    public function test_credit_invoice_total_paid_increases_with_credit_and_decreases_with_debit_transactions(): void
    {
        $invoice = $this->createInvoice(['type' => 'credit', 'price' => 1000]);
        $bankAccount = $this->createBankAccount();

        $this->createTransaction($invoice, $bankAccount, 'credit', 400);
        $invoice->refresh();
        $this->assertEquals(400, $invoice->total_paid);
        $this->assertEquals(600, $invoice->balance);

        // Estorno: um débito reduz o total_paid de uma fatura de crédito
        $this->createTransaction($invoice, $bankAccount, 'debit', 100);
        $invoice->refresh();
        $this->assertEquals(300, $invoice->total_paid);
        $this->assertEquals(700, $invoice->balance);
    }

    public function test_debit_invoice_total_paid_increases_with_debit_and_decreases_with_credit_transactions(): void
    {
        $invoice = $this->createInvoice(['type' => 'debit', 'price' => 500]);
        $bankAccount = $this->createBankAccount();

        $this->createTransaction($invoice, $bankAccount, 'debit', 200);
        $invoice->refresh();
        $this->assertEquals(200, $invoice->total_paid);
        $this->assertEquals(300, $invoice->balance);

        // Estorno: um crédito reduz o total_paid de uma fatura de débito
        $this->createTransaction($invoice, $bankAccount, 'credit', 50);
        $invoice->refresh();
        $this->assertEquals(150, $invoice->total_paid);
        $this->assertEquals(350, $invoice->balance);
    }

    public function test_invoice_status_is_paid_even_when_past_due_date(): void
    {
        $invoice = $this->createInvoice([
            'price' => 100,
            'total_paid' => 100,
            'balance' => 0,
            'date_due' => now()->subDays(5)->toDateString(),
        ]);

        $this->assertEquals(Invoice::STATUS_PAID, $invoice->calculateStatus());
    }

    public function test_invoice_status_is_overdue_when_past_due_date_and_unpaid(): void
    {
        $invoice = $this->createInvoice([
            'price' => 100,
            'total_paid' => 0,
            'date_due' => now()->subDays(5)->toDateString(),
        ]);

        $this->assertEquals(Invoice::STATUS_OVERDUE, $invoice->calculateStatus());
    }

    public function test_invoice_status_is_pending_when_not_due_yet(): void
    {
        $invoice = $this->createInvoice([
            'price' => 100,
            'total_paid' => 0,
            'date_due' => now()->addDays(5)->toDateString(),
        ]);

        $this->assertEquals(Invoice::STATUS_PENDING, $invoice->calculateStatus());
    }

    public function test_bank_account_balance_combines_initial_balance_with_credit_and_debit_transactions(): void
    {
        $invoice = $this->createInvoice();
        $bankAccount = $this->createBankAccount(['initial_balance' => 1000]);

        $this->createTransaction($invoice, $bankAccount, 'credit', 500);
        $this->createTransaction($invoice, $bankAccount, 'debit', 200);

        $this->assertEquals(1300, $bankAccount->fresh()->balance);
    }

    public function test_split_into_installments_sums_exactly_to_total_with_remainder_on_last(): void
    {
        $amounts = Invoice::splitIntoInstallments(100, 3);

        $this->assertCount(3, $amounts);
        $this->assertEquals(100, round(array_sum($amounts), 2));
        $this->assertEquals(33.34, $amounts[2]); // resto do arredondamento vai para a última parcela

        $amounts = Invoice::splitIntoInstallments(10.01, 4);
        $this->assertEquals(10.01, round(array_sum($amounts), 2));

        $this->assertEquals([], Invoice::splitIntoInstallments(100, 0));
    }

    private function createProposalWithCreditInvoices(float $totalPrice, array $installments): Proposal
    {
        $opportunity = Opportunity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
        ]);

        $proposal = Proposal::factory()->create([
            'account_id' => $this->account->id,
            'opportunity_id' => $opportunity->id,
            'total_price' => $totalPrice,
            'installment_quantity' => count($installments),
        ]);

        foreach ($installments as $index => $installment) {
            $this->createInvoice([
                'proposal_id' => $proposal->id,
                'type' => 'credit',
                'price' => $installment['price'],
                'balance' => $installment['price'] - ($installment['total_paid'] ?? 0),
                'total_paid' => $installment['total_paid'] ?? 0,
                'date_due' => now()->addMonths($index + 1)->toDateString(),
            ]);
        }

        return $proposal;
    }

    public function test_redistribute_invoices_keeps_paid_invoice_and_splits_remaining_balance_evenly(): void
    {
        $proposal = $this->createProposalWithCreditInvoices(900, [
            ['price' => 300, 'total_paid' => 300], // já paga, deve ser preservada
            ['price' => 300, 'total_paid' => 0],
            ['price' => 300, 'total_paid' => 0],
        ]);

        $result = $proposal->redistributeInvoices(4);

        $this->assertEquals('success', $result['status']);

        $creditInvoices = $proposal->invoices()->where('type', 'credit')->get();
        $this->assertCount(4, $creditInvoices);
        $this->assertEquals(900, round($creditInvoices->sum('price'), 2));
        $this->assertEquals(1, $creditInvoices->where('total_paid', '>', 0)->count());
    }

    public function test_redistribute_invoices_fails_when_reducing_below_paid_installments_count(): void
    {
        $proposal = $this->createProposalWithCreditInvoices(900, [
            ['price' => 300, 'total_paid' => 300],
            ['price' => 300, 'total_paid' => 300],
            ['price' => 300, 'total_paid' => 0],
        ]);

        $result = $proposal->redistributeInvoices(1);

        $this->assertEquals('error', $result['status']);
        $this->assertEquals(3, $proposal->invoices()->where('type', 'credit')->count());
    }

    public function test_updating_a_credit_invoice_price_redistributes_remaining_invoices_via_api(): void
    {
        $proposal = $this->createProposalWithCreditInvoices(900, [
            ['price' => 300, 'total_paid' => 0],
            ['price' => 300, 'total_paid' => 0],
            ['price' => 300, 'total_paid' => 0],
        ]);

        $firstInvoice = $proposal->invoices()->where('type', 'credit')->orderBy('date_due')->first();

        $response = $this->putJson("/api/invoices/{$firstInvoice->id}", [
            'price' => 600,
        ]);

        $response->assertStatus(200);

        $creditInvoices = $proposal->invoices()->where('type', 'credit')->get();
        $this->assertEquals(900, round($creditInvoices->sum('price'), 2));
        $this->assertEquals(600, $creditInvoices->firstWhere('id', $firstInvoice->id)->price);
    }

    public function test_updating_a_credit_invoice_price_beyond_proposal_total_is_rejected(): void
    {
        $proposal = $this->createProposalWithCreditInvoices(900, [
            ['price' => 300, 'total_paid' => 0],
            ['price' => 300, 'total_paid' => 0],
            ['price' => 300, 'total_paid' => 0],
        ]);

        $firstInvoice = $proposal->invoices()->where('type', 'credit')->orderBy('date_due')->first();

        $response = $this->putJson("/api/invoices/{$firstInvoice->id}", [
            'price' => 1000,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(300, $firstInvoice->fresh()->price);
    }
}
