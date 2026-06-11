<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $role = auth()->user()->role;
        $name = auth()->user()->name;

        // Simpan pesan sukses ke session
        $roleLabel = match ($role) {
            'owner' => 'Pemilik',
            'kasir' => 'Kasir',
            'chef' => 'Chef',
            default => $role,
        };

        session()->flash('login_success', "Selamat datang, $name! Anda berhasil masuk sebagai $roleLabel.");

        return match ($role) {
            'owner' => redirect()->route('owner.laporan.index'),
            'kasir' => redirect()->route('kasir.pesanan.index'),
            'chef' => redirect()->route('chef.pesanan.index'),
            default => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
