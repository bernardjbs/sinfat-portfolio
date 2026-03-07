<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_valid_credentials_return_200_with_redirect(): void
    {
        User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->postJson('/login', [
            'email'    => 'admin@test.com',
            'password' => 'secret123',
        ])
            ->assertOk()
            ->assertJsonPath('redirect', '/admin');
    }

    public function test_invalid_credentials_return_401(): void
    {
        User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->postJson('/login', [
            'email'    => 'admin@test.com',
            'password' => 'wrongpassword',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid credentials');
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->postJson('/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_logout_clears_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/logout')
            ->assertOk()
            ->assertJsonPath('redirect', '/');
    }

    public function test_unauthenticated_logout_is_rejected(): void
    {
        $this->postJson('/logout')->assertUnauthorized();
    }
}
