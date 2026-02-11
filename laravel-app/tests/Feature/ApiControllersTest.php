<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiControllersTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_register_endpoint(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user',
                        'token',
                    ],
                ]);
    }

    public function test_auth_login_endpoint(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user',
                        'token',
                    ],
                ]);
    }

    public function test_user_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_user_endpoint(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                ]);
    }

    public function test_users_list_endpoint(): void
    {
        $user = User::factory()->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'current_page',
                        'data',
                        'first_page_url',
                        'last_page_url',
                        'next_page_url',
                        'path',
                        'per_page',
                        'prev_page_url',
                        'to',
                        'total',
                    ],
                ]);
    }

    public function test_extensions_require_authentication(): void
    {
        $response = $this->getJson('/api/extensions');

        $response->assertStatus(401);
    }

    public function test_call_logs_require_authentication(): void
    {
        $response = $this->getJson('/api/call-logs');

        $response->assertStatus(401);
    }

    public function test_settings_require_authentication(): void
    {
        $response = $this->getJson('/api/settings');

        $response->assertStatus(401);
    }
}
