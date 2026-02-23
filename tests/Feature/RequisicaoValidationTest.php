<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use Tests\TestCase;

class RequisicaoValidationTest extends TestCase
{
    public function test_rejects_invalid_book_id()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $response = $this->post('/loans', [
            'book_id' => 999999, 
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['book_id']);
        
        $this->assertDatabaseCount('loans', 0);
    }

   /* public function test_creates_loan_with_valid_book_id()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/loans', [
            'book_id' => $book->id, 
        ]);

        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseCount('loans', 1);
    }*/
}
