<?php

namespace App\Services;

use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final readonly class AccountService
{
    /**
     * Get paginated list of non-admin accounts with search and filtering.
     */
    public function getPaginatedAccounts(
        ?string $search = null,
        ?string $role = null,
        ?string $category = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = User::query()
            ->where('role', '!=', UserRole::Admin);

        if (! empty($search)) {
            $searchTerm = trim($search);
            $query->where(function ($q) use ($searchTerm): void {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if (! empty($role)) {
            $query->where('role', $role);
        }

        if (! empty($category)) {
            $query->where('user_category', $category);
        }

        return $query->latest('created_at')->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new Petugas or Pengguna account with an auto-generated secure password.
     *
     * @param  array{name: string, email: string, role: string, user_category?: ?string}  $data
     * @return array{user: User, plain_password: string}
     */
    public function createAccount(array $data): array
    {
        $plainPassword = Str::password(length: 12, letters: true, numbers: true, symbols: true, spaces: false);

        $role = $data['role'] instanceof UserRole ? $data['role'] : UserRole::from($data['role']);

        $category = null;
        if ($role === UserRole::Pengguna && ! empty($data['user_category'])) {
            $category = $data['user_category'] instanceof UserCategory
                ? $data['user_category']
                : UserCategory::from($data['user_category']);
        }

        $user = User::query()->create([
            'name' => trim($data['name']),
            'email' => Str::lower(trim($data['email'])),
            'password' => $plainPassword,
            'role' => $role,
            'user_category' => $category,
        ]);

        return [
            'user' => $user,
            'plain_password' => $plainPassword,
        ];
    }

    /**
     * Directly reset the password for a given user account.
     */
    public function resetPassword(User $user, string $newPassword, string $adminId): void
    {
        $user->update([
            'password' => $newPassword,
        ]);

        Log::info(sprintf(
            '[AUDIT] Admin ID %s reset password for User ID %s (%s) at %s',
            $adminId,
            $user->id,
            $user->email,
            now()->toIso8601String(),
        ));
    }
}
