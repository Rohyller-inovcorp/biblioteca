<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Book;
use Tests\TestCase;

class BookCreationTest extends TestCase
{
    public function test_cria_um_livro_corretamente()
    {
        // 1. Criar utilizador autenticado com permissões de admin
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // 2. Criar publisher
        $publisher = Publisher::factory()->create();

        // 3. Criar autores
        $authors = Author::factory()->count(2)->create();

        // 4. Enviar requisição para criar livro
        $response = $this->post('/books', [
            'isbn' => '1234567890',
            'name' => 'Livro de Teste',
            'publisher_id' => $publisher->id,
            'bibliography' => 'Descrição teste',
            'price' => 19.99,
            'authors' => $authors->pluck('id')->toArray(),
        ]);

        // 5. Verificar redirect
        $response->assertRedirect('/books');

        // 6. Verificar livro na BD
        $this->assertDatabaseHas('books', [
            'isbn' => '1234567890',
            'name' => 'Livro de Teste',
            'publisher_id' => $publisher->id,
        ]);

        // 7. Verificar relação autores <-> livro
        $book = Book::where('isbn', '1234567890')->first();
        $this->assertEquals(2, $book->authors()->count());
    }
}
