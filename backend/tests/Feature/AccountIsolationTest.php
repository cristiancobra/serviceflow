<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Proposal;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Account $accountA;
    protected Account $accountB;
    protected User $userA;
    protected User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountA = Account::factory()->create();
        $this->accountB = Account::factory()->create();

        $this->userA = User::factory()->create(['account_id' => $this->accountA->id]);
        $this->userB = User::factory()->create(['account_id' => $this->accountB->id]);
    }

    /** @dataProvider directColumnModels */
    public function test_index_does_not_leak_records_from_other_account(string $modelClass, string $endpoint): void
    {
        $recordFromB = $this->makeRecordForAccount($modelClass, $this->accountB, $this->userB);

        Sanctum::actingAs($this->userA);

        $response = $this->getJson($endpoint);

        $response->assertStatus(200);
        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertNotContains($recordFromB->id, $ids);
    }

    /** @dataProvider directColumnModels */
    public function test_show_returns_404_for_record_from_other_account(string $modelClass, string $endpoint): void
    {
        $recordFromB = $this->makeRecordForAccount($modelClass, $this->accountB, $this->userB);

        Sanctum::actingAs($this->userA);

        $response = $this->getJson("{$endpoint}/{$recordFromB->id}");

        $response->assertStatus(404);
    }

    public function directColumnModels(): array
    {
        return [
            'companies' => [Company::class, '/api/companies'],
            'opportunities' => [Opportunity::class, '/api/opportunities'],
            'tasks' => [Task::class, '/api/tasks'],
            'leads' => [Lead::class, '/api/leads'],
            'proposals' => [Proposal::class, '/api/proposals'],
        ];
    }

    public function test_user_index_does_not_leak_users_from_other_account(): void
    {
        Sanctum::actingAs($this->userA);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertNotContains($this->userB->id, $ids);
        $this->assertContains($this->userA->id, $ids);
    }

    public function test_user_show_returns_404_for_user_from_other_account(): void
    {
        Sanctum::actingAs($this->userA);

        $response = $this->getJson("/api/users/{$this->userB->id}");

        $response->assertStatus(404);
    }

    public function test_creating_a_record_ignores_a_spoofed_account_id_from_another_account(): void
    {
        Sanctum::actingAs($this->userA);

        $opportunity = Opportunity::create([
            'account_id' => $this->accountB->id,
            'user_id' => $this->userA->id,
            'name' => 'Tentativa de spoof',
        ]);

        $this->assertEquals($this->accountA->id, $opportunity->fresh()->account_id);
    }

    private function makeRecordForAccount(string $modelClass, Account $account, User $user)
    {
        switch ($modelClass) {
            case Company::class:
                return Company::factory()->create(['account_id' => $account->id]);
            case Lead::class:
                return Lead::factory()->create(['account_id' => $account->id, 'user_id' => $user->id]);
            case Opportunity::class:
                return Opportunity::factory()->create([
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                ]);
            case Task::class:
                return Task::factory()->create([
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                ]);
            case Proposal::class:
                $opportunity = Opportunity::factory()->create([
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                ]);

                return Proposal::factory()->create([
                    'account_id' => $account->id,
                    'opportunity_id' => $opportunity->id,
                ]);
            default:
                throw new \InvalidArgumentException("Sem fábrica configurada para {$modelClass}");
        }
    }
}
