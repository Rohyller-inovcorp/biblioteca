<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_request_book_without_stock()
    {
        config(['scout.driver' => 'collection']);

        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 0]); // 0 stock

        $this->actingAs($user);

        $response = $this->post('/loans', [
            'book_id' => $book->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['message']);

        $this->assertDatabaseCount('loans', 0);
    }
}
