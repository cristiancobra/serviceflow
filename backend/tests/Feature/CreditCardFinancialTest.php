<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\CreditCard;
use App\Models\CreditCardInvoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Cobre o ciclo do cartão de crédito: compra parcelada caindo na fatura certa
 * conforme o dia de fechamento, fechamento manual da fatura e pagamento (a
 * única transação real entre contas em todo o fluxo).
 */
class CreditCardFinancialTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $user;
    protected BankAccount $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
        $this->user = User::factory()->create(['account_id' => $this->account->id]);

        $this->bankAccount = BankAccount::create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'account_name' => 'Conta Teste',
            'account_number' => '12345-6',
            'bank_name' => 'Banco Teste',
        ]);

        Sanctum::actingAs($this->user);
    }

    private function createCreditCard(array $overrides = []): CreditCard
    {
        $response = $this->postJson('/api/credit_cards', array_merge([
            'name' => 'Cartao Teste',
            'closing_day' => 5,
            'due_day' => 12,
            'credit_limit' => 5000,
        ], $overrides));

        $response->assertCreated();

        return CreditCard::findOrFail($response->json('data.id'));
    }

    public function test_purchase_installments_fall_into_the_correct_invoices_by_closing_day()
    {
        $card = $this->createCreditCard();

        // Compra em 10/mar (depois do fechamento dia 5) parcelada em 3x
        // deve cair nas faturas de abril, maio e junho.
        $response = $this->postJson('/api/credit_card_charges', [
            'credit_card_id' => $card->id,
            'description' => 'Notebook',
            'amount' => 300,
            'purchase_date' => '2026-03-10',
            'installment_total' => 3,
        ]);

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));

        $invoices = CreditCardInvoice::where('credit_card_id', $card->id)
            ->orderBy('reference_year')->orderBy('reference_month')
            ->get();

        $this->assertCount(3, $invoices);
        $this->assertEquals([4, 5, 6], $invoices->pluck('reference_month')->all());
        $this->assertEquals('2026-04-05', $invoices[0]->closing_date->toDateString());
        $this->assertEquals('2026-04-12', $invoices[0]->due_date->toDateString());
        $this->assertEquals(100.0, (float) $invoices[0]->total_amount);
        $this->assertEquals(CreditCardInvoice::STATUS_OPEN, $invoices[0]->status);
    }

    public function test_paying_a_closed_invoice_updates_balance_and_status()
    {
        $card = $this->createCreditCard();

        // Compra no dia 1 (antes do fechamento dia 5) de um mês bem no futuro,
        // para que o vencimento da fatura nunca fique no passado em relação ao "agora"
        // do ambiente de testes e o status não vire "overdue" antes da hora.
        $purchaseDate = Carbon::now()->addMonths(2)->startOfMonth()->toDateString();

        $this->postJson('/api/credit_card_charges', [
            'credit_card_id' => $card->id,
            'description' => 'Assinatura',
            'amount' => 150,
            'purchase_date' => $purchaseDate,
        ])->assertOk();

        $invoice = CreditCardInvoice::where('credit_card_id', $card->id)->firstOrFail();

        // Não é permitido pagar uma fatura ainda aberta.
        $this->postJson("/api/credit_card_invoices/{$invoice->id}/pay", [
            'bank_account_id' => $this->bankAccount->id,
            'amount' => 150,
            'transaction_date' => $purchaseDate,
            'method' => 'pix',
        ])->assertStatus(422);

        $this->postJson("/api/credit_card_invoices/{$invoice->id}/close")
            ->assertOk()
            ->assertJsonPath('data.status', 'closed');

        $this->postJson("/api/credit_card_invoices/{$invoice->id}/pay", [
            'bank_account_id' => $this->bankAccount->id,
            'amount' => 150,
            'transaction_date' => $purchaseDate,
            'method' => 'pix',
        ])->assertOk()
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonPath('data.balance', '0.00');
    }

    public function test_cannot_delete_a_charge_from_a_closed_invoice()
    {
        $card = $this->createCreditCard();

        $charge = $this->postJson('/api/credit_card_charges', [
            'credit_card_id' => $card->id,
            'description' => 'Compra',
            'amount' => 50,
            'purchase_date' => Carbon::now()->addMonths(2)->startOfMonth()->toDateString(),
        ])->json('data.0');

        $invoice = CreditCardInvoice::where('credit_card_id', $card->id)->firstOrFail();
        $this->postJson("/api/credit_card_invoices/{$invoice->id}/close")->assertOk();

        $this->deleteJson("/api/credit_card_charges/{$charge['id']}")
            ->assertStatus(422);
    }
}
