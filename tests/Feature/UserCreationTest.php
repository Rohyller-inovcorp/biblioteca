<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('cria um utilizador corretamente', function () {

    // Autenticar un admin
    $admin = User::factory()->create([
        'role' => 'admin'
    ]);

    $this->actingAs($admin);

    // Crear un nuevo usuario
    $response = $this->post('/users', [
        'name' => 'Rohyller',
        'email' => 'rohyller@example.com',
        'password' => 'password123',
        'role' => 'admin',
    ]);

    // Verifica que hace redirect correctamente
    $response->assertRedirect(route('users.index'));

    // Verifica que el usuario se creó en la BD
    $this->assertDatabaseHas('users', [
        'email' => 'rohyller@example.com',
        'role' => 'admin',
    ]);
});
