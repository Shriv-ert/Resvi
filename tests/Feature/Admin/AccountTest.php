<?php

namespace Tests\Feature\Admin;

use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::query()->create([
            'name' => 'Admin PPK',
            'email' => 'admin@ppk.test',
            'password' => 'PPK-Admin-2026!',
            'role' => UserRole::Admin,
            'user_category' => null,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin_accounts(): void
    {
        $response = $this->get(route('admin.accounts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_accounts(): void
    {
        $pengguna = User::query()->create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $response = $this->actingAs($pengguna)->get(route('admin.accounts.index'));

        $response->assertForbidden();
    }

    public function test_petugas_cannot_access_admin_accounts(): void
    {
        $petugas = User::query()->create([
            'name' => 'Petugas Test',
            'email' => 'petugas@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Petugas,
            'user_category' => null,
        ]);

        $response = $this->actingAs($petugas)->get(route('admin.accounts.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_accounts_directory_and_admin_is_excluded(): void
    {
        $petugas = User::query()->create([
            'name' => 'Budi Petugas',
            'email' => 'budi.petugas@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Petugas,
            'user_category' => null,
        ]);

        $pengguna = User::query()->create([
            'name' => 'Siti Mahasiswa',
            'email' => 'siti.mhs@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $otherAdmin = User::query()->create([
            'name' => 'Other Admin',
            'email' => 'other.admin@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Admin,
            'user_category' => null,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.accounts.index'));

        $response->assertOk();
        $response->assertSee('Budi Petugas');
        $response->assertSee('Siti Mahasiswa');
        $response->assertDontSee('Other Admin');
    }

    public function test_admin_can_create_petugas_account_with_null_category(): void
    {
        $payload = [
            'name' => 'Ahmad Petugas',
            'email' => 'ahmad.petugas@ppk.test',
            'role' => 'petugas',
            'user_category' => 'mahasiswa', // Should be forced to null by service/request
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.accounts.create'))
            ->post(route('admin.accounts.store'), $payload);

        $response->assertRedirect(route('admin.accounts.created'));
        $response->assertSessionHas('created_account');

        $createdSession = session('created_account');
        $this->assertNotEmpty($createdSession['plain_password']);
        $this->assertSame('ahmad.petugas@ppk.test', $createdSession['email']);

        $user = User::query()->where('email', 'ahmad.petugas@ppk.test')->first();
        $this->assertNotNull($user);
        $this->assertSame(UserRole::Petugas, $user->role);
        $this->assertNull($user->user_category);

        // Verify that the generated password allows the Petugas to authenticate
        $this->assertTrue(Hash::check($createdSession['plain_password'], $user->password));
    }

    public function test_admin_can_create_pengguna_account_with_category(): void
    {
        $payload = [
            'name' => 'Dewi Dosen',
            'email' => 'dewi.dosen@ppk.test',
            'role' => 'pengguna',
            'user_category' => 'dosen',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.accounts.create'))
            ->post(route('admin.accounts.store'), $payload);

        $response->assertRedirect(route('admin.accounts.created'));
        $response->assertSessionHas('created_account');

        $user = User::query()->where('email', 'dewi.dosen@ppk.test')->first();
        $this->assertNotNull($user);
        $this->assertSame(UserRole::Pengguna, $user->role);
        $this->assertSame(UserCategory::Dosen, $user->user_category);

        $createdSession = session('created_account');
        $this->assertTrue(Hash::check($createdSession['plain_password'], $user->password));
    }

    public function test_creating_pengguna_without_category_fails_validation(): void
    {
        $payload = [
            'name' => 'User Incomplete',
            'email' => 'incomplete@ppk.test',
            'role' => 'pengguna',
            'user_category' => '',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.accounts.create'))
            ->post(route('admin.accounts.store'), $payload);

        $response->assertRedirect(route('admin.accounts.create'));
        $response->assertSessionHasErrors('user_category');

        $this->assertDatabaseMissing('users', ['email' => 'incomplete@ppk.test']);
    }

    public function test_creating_account_with_duplicate_email_fails_validation(): void
    {
        User::query()->create([
            'name' => 'Existing User',
            'email' => 'existing@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $payload = [
            'name' => 'Duplicate Attempt',
            'email' => 'existing@ppk.test',
            'role' => 'pengguna',
            'user_category' => 'mahasiswa',
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.accounts.create'))
            ->post(route('admin.accounts.store'), $payload);

        $response->assertRedirect(route('admin.accounts.create'));
        $response->assertSessionHasErrors('email');
    }

    public function test_created_page_redirects_if_session_has_no_created_account(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.accounts.created'));

        $response->assertRedirect(route('admin.accounts.index'));
        $response->assertSessionHas('warning');
    }

    public function test_created_page_displays_credential_when_flashed(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession([
                'created_account' => [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'name' => 'Baru Test',
                    'email' => 'baru@ppk.test',
                    'role' => UserRole::Petugas,
                    'category' => null,
                    'plain_password' => 'abc123XYZ!@#',
                ],
            ])
            ->get(route('admin.accounts.created'));

        $response->assertOk();
        $response->assertSee('Baru Test');
        $response->assertSee('baru@ppk.test');
        $response->assertSee('abc123XYZ!@#');
    }

    public function test_admin_can_view_account_details(): void
    {
        $user = User::query()->create([
            'name' => 'Rina Mahasiswa',
            'email' => 'rina@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.accounts.show', $user));

        $response->assertOk();
        $response->assertSee('Rina Mahasiswa');
        $response->assertSee('rina@ppk.test');
        $response->assertSee('Mahasiswa');
    }

    public function test_admin_can_reset_user_password_without_knowing_old_password(): void
    {
        $user = User::query()->create([
            'name' => 'Doni Petugas',
            'email' => 'doni@ppk.test',
            'password' => 'old_secret_pwd',
            'role' => UserRole::Petugas,
            'user_category' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.accounts.show', $user))
            ->post(route('admin.accounts.reset-password', $user), [
                'password' => 'new_secret_pwd_2026!',
                'password_confirmation' => 'new_secret_pwd_2026!',
            ]);

        $response->assertRedirect(route('admin.accounts.show', $user));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('new_secret_pwd_2026!', $user->password));
    }

    public function test_reset_password_fails_if_confirmation_mismatches_or_too_short(): void
    {
        $user = User::query()->create([
            'name' => 'Doni Petugas',
            'email' => 'doni@ppk.test',
            'password' => 'old_secret_pwd',
            'role' => UserRole::Petugas,
            'user_category' => null,
        ]);

        // Mismatch
        $response = $this->actingAs($this->admin)
            ->from(route('admin.accounts.show', $user))
            ->post(route('admin.accounts.reset-password', $user), [
                'password' => 'new_secret_pwd_1',
                'password_confirmation' => 'new_secret_pwd_2',
            ]);

        $response->assertRedirect(route('admin.accounts.show', $user));
        $response->assertSessionHasErrors('password');

        // Too short (< 8 chars)
        $response2 = $this->actingAs($this->admin)
            ->from(route('admin.accounts.show', $user))
            ->post(route('admin.accounts.reset-password', $user), [
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response2->assertRedirect(route('admin.accounts.show', $user));
        $response2->assertSessionHasErrors('password');
    }

    public function test_admin_can_search_and_filter_accounts(): void
    {
        User::query()->create([
            'name' => 'Fajar Mahasiswa',
            'email' => 'fajar@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        User::query()->create([
            'name' => 'Gilang Dosen',
            'email' => 'gilang@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);

        // Search name
        $response = $this->actingAs($this->admin)->get(route('admin.accounts.index', ['search' => 'Fajar']));
        $response->assertOk();
        $response->assertSee('Fajar Mahasiswa');
        $response->assertDontSee('Gilang Dosen');

        // Filter category
        $response2 = $this->actingAs($this->admin)->get(route('admin.accounts.index', ['category' => 'dosen']));
        $response2->assertOk();
        $response2->assertSee('Gilang Dosen');
        $response2->assertDontSee('Fajar Mahasiswa');
    }
}
