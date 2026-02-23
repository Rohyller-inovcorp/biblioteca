<?php

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('permite que um utilizador crie uma requisição de livro', function () {

    // 1. Criar utilizador
    $user = User::factory()->create();

    // 2. Criar livro
    $livro = Book::factory()->create();

    // 3. Autenticar utilizador
    $this->actingAs($user);

    // 4. Enviar requisição
    $response = $this->postJson('/loans', [
        'book_id' => $livro->id,
        'data_requisicao' => now()->toDateString(),
    ]);

    // 5. Verificar resposta
    $response->assertRedirect();

    // 6. Verificar BD
    $this->assertDatabaseHas('loans', [
        'book_id' => $livro->id,
        'user_id' => $user->id,
    ]);
});
