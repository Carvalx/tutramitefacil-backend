<?php

use App\Models\User;

test('un usuario puede registrarse y recibe un token JWT', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['access_token', 'token_type', 'user']);

    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

test('un usuario puede iniciar sesión con credenciales correctas', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['access_token', 'token_type']);
});

test('el login falla con credenciales incorrectas', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password-incorrecta',
    ]);

    $response->assertStatus(401);
});
