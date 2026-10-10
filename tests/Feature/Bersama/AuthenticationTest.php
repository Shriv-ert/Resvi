<?php

namespace Tests\Feature\Bersama;

use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_can_open_the_public_homepage_and_login_form(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
    }

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'mahasiswa@example.test',
            'password' => Hash::make('secret-password'),
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $this->post('/login', [
            'email' => 'mahasiswa@example.test',
            'password' => 'secret-password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_redirects_to_facility_management(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('admin-secret-password'),
        ]);

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'admin-secret-password',
        ])->assertRedirect(route('admin.facilities.index'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_invalid_credentials_do_not_authenticate_a_guest(): void
    {
        User::factory()->create([
            'email' => 'mahasiswa@example.test',
            'password' => Hash::make('correct-password'),
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'mahasiswa@example.test',
            'password' => 'incorrect-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }
}
