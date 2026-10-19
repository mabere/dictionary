<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_use_dictionary_context(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk ke akun')
            ->assertSee('Kamus Tolaki')
            ->assertDontSee('Perpustakaan Digital');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Daftar akun')
            ->assertSee('Kontributor Kamus');
    }

    public function test_public_user_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Kontributor Kamus',
            'email' => 'kontributor@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dictionary.index'));
        $this->assertAuthenticated();
        $this->assertSame('user', User::where('email', 'kontributor@example.com')->value('role'));
    }

    public function test_public_user_login_redirects_to_dictionary(): void
    {
        User::factory()->create([
            'email' => 'public@example.com',
            'password' => 'password123',
            'role' => 'user',
        ]);

        $this->post(route('login.store'), [
            'email' => 'public@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('dictionary.index'));
    }

    public function test_editor_login_redirects_to_dashboard(): void
    {
        User::factory()->create([
            'email' => 'editor@example.com',
            'password' => 'password123',
            'role' => 'editor',
        ]);

        $this->post(route('login.store'), [
            'email' => 'editor@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_assessor_login_redirects_to_dashboard(): void
    {
        User::factory()->create([
            'email' => 'assessor@example.com',
            'password' => 'password123',
            'role' => 'assessor',
        ]);

        $this->post(route('login.store'), [
            'email' => 'assessor@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_failed_login_limit_returns_form_error_instead_of_429(): void
    {
        User::factory()->create([
            'email' => 'limited@example.com',
            'password' => 'password123',
            'role' => 'assessor',
        ]);

        RateLimiter::clear('login|limited@example.com|127.0.0.1');

        for ($attempt = 1; $attempt <= 8; $attempt++) {
            $this->from(route('login'))->post(route('login.store'), [
                'email' => 'limited@example.com',
                'password' => 'salah-password',
            ])->assertRedirect(route('login'));
        }

        $this->from(route('login'))->post(route('login.store'), [
            'email' => 'limited@example.com',
            'password' => 'salah-password',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_public_registration_ignores_dashboard_intended_url(): void
    {
        $this->get(route('register'))->assertOk();

        $response = $this
            ->withSession(['url.intended' => route('dashboard')])
            ->post(route('register.store'), [
                'name' => 'User Publik',
                'email' => 'user-publik@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('dictionary.index'));
        $this->assertAuthenticated();
        $this->assertSame('user', User::where('email', 'user-publik@example.com')->value('role'));
    }

    public function test_public_user_is_redirected_from_dashboard_to_dictionary(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('dictionary.index'))
            ->assertSessionHasErrors('access');
    }

    public function test_public_header_hides_dashboard_for_regular_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->get(route('dictionary.index'))
            ->assertOk()
            ->assertDontSee('Dashboard')
            ->assertSee('Logout');
    }

    public function test_public_user_is_redirected_from_restricted_routes_to_dictionary(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        foreach (['/dashboard', '/admin', '/admin/dictionary-import', '/admin/dictionary-export'] as $path) {
            $this->actingAs($user)
                ->get($path)
                ->assertRedirect(route('dictionary.index'));
        }
    }
}
