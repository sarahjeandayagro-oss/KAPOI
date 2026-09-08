<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinePaymentChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_overpayment_on_fine_records_return_change_in_transaction(): void
    {
        $this->withoutMiddleware();

        $response = $this->from('/')->post(route('admin.financial-transactions.store'), [
            'transaction_type' => 'Fine',
            'user_id' => 'STU-1001',
            'description' => 'Overdue book fine',
            'amount' => '40.00',
            'received_amount' => '100.00',
            'status' => 'Paid',
            'transaction_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();

        $transaction = DB::table('financial_transactions')->latest('id')->first();

        $this->assertNotNull($transaction);
        $this->assertSame('40.00', (string) $transaction->amount);
        $this->assertSame('100.00', (string) $transaction->received_amount);
        $this->assertSame('60.00', (string) $transaction->change_amount);
        $this->assertSame('returned', $transaction->change_status);
    }
}
