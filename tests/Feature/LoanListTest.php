<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Loan;
use App\Models\Book;
use Tests\TestCase;

class LoanListTest extends TestCase
{
    public function test_user_sees_only_his_own_loans()
    {
        // 1. Criar dois utilizadores
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // 2. Criar livros
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        // 3. Criar requisições para cada utilizador
        Loan::factory()->create([
            'user_id' => $userA->id,
            'book_id' => $book1->id,
        ]);

        Loan::factory()->create([
            'user_id' => $userB->id,
            'book_id' => $book2->id,
        ]);

        $this->actingAs($userA);

        $response = $this->get('/loans');
        $response->assertStatus(200);

        $response->assertInertia(function ($page) use ($userA) {
            $page->component('Loans/Index')
                ->has('loans.data', 1)
                ->where('loans.data.0.user_id', $userA->id);
        });
    }
}
