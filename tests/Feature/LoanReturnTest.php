<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Loan;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LoanReturnTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_return_a_book()
    {
        config(['scout.driver' => 'collection']);

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => now(),
            'expected_return_date' => now()->addDays(5),
            'actual_return_date' => null,
        ]);

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'actual_return_date' => null,
        ]);

        $this->actingAs($user);

        $returnedAt = Carbon::create(2026, 2, 22, 12, 0, 0);
        Carbon::setTestNow($returnedAt);

        $response = $this->post("/loans/{$loan->id}/return");

        $response->assertRedirect();

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'actual_return_date' => $returnedAt->toDateTimeString(),
        ]);

        $this->assertNotNull(
            Loan::find($loan->id)->actual_return_date,
            'O emprestimo nao foi devolvido.'
        );

        Carbon::setTestNow();
    }
}
