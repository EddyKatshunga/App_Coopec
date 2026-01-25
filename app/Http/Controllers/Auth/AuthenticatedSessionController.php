<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

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
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Redirection par rôle
        return redirect()->intended($this->redirectToRole($request->user()));
    }


    protected function redirectToRole($user)
    {
        if ($user->hasRole('pca')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('chef_agence')) {
            return route('chef_agence.dashboard');
        }

        if ($user->hasRole('agent_guichet')) {
            return route('agent.dashboard');
        }

        if ($user->hasRole('membre')) {
            return route('membre.dashboard');
        }

        // Par défaut
        return route('dashboard');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
