<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\StoreAccountRequest;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
    ) {}

    /**
     * Display a listing of user and petugas accounts.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $role = $request->query('role');
        $category = $request->query('category');

        $accounts = $this->accountService->getPaginatedAccounts(
            search: is_string($search) ? $search : null,
            role: is_string($role) ? $role : null,
            category: is_string($category) ? $category : null,
        );

        return view('admin.accounts.index', [
            'accounts' => $accounts,
            'search' => $search,
            'selectedRole' => $role,
            'selectedCategory' => $category,
        ]);
    }

    /**
     * Show the form for creating a new account.
     */
    public function create(): View
    {
        return view('admin.accounts.create');
    }

    /**
     * Store a newly created account in storage.
     */
    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $result = $this->accountService->createAccount($request->validated());

        session()->flash('created_account', [
            'id' => $result['user']->id,
            'name' => $result['user']->name,
            'email' => $result['user']->email,
            'role' => $result['user']->role,
            'category' => $result['user']->user_category,
            'plain_password' => $result['plain_password'],
        ]);

        return redirect()->route('admin.accounts.created')
            ->with('status', 'Akun berhasil dibuat.');
    }

    /**
     * Display the one-time credential confirmation screen.
     */
    public function created(): View|RedirectResponse
    {
        if (! session()->has('created_account')) {
            return redirect()->route('admin.accounts.index')
                ->with('warning', 'Kata sandi awal hanya ditampilkan satu kali sesaat setelah akun dibuat.');
        }

        return view('admin.accounts.created', [
            'account' => session('created_account'),
        ]);
    }

    /**
     * Display the specified account details.
     */
    public function show(User $user): View
    {
        return view('admin.accounts.show', [
            'user' => $user,
        ]);
    }

    /**
     * Reset the password for the specified user.
     */
    public function resetPassword(ResetPasswordRequest $request, User $user): RedirectResponse
    {
        $this->accountService->resetPassword(
            user: $user,
            newPassword: $request->validated('password'),
            adminId: (string) $request->user()?->id,
        );

        return redirect()->route('admin.accounts.show', $user)
            ->with('status', 'Kata sandi baru berhasil ditetapkan.');
    }
}
