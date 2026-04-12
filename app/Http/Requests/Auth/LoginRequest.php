<?php

namespace App\Http\Requests\Auth;

use App\Models\Membre;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'], // 🔥 email OU numéro
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->input('login');

        // 🔍 CAS 1 : EMAIL (Admin, agents, etc.)
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {

            if (! Auth::attempt([
                'email' => $login,
                'password' => $this->password,
            ], false)) {

                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'login' => 'Identifiants incorrects.',
                ]);
            }
        }

        // 🔍 CAS 2 : NUMÉRO D’IDENTIFICATION (Membre)
        else {

            $membre = Membre::where('numero_identification', $login)
                ->with('user')
                ->first();

            if (! $membre || ! $membre->user) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'login' => 'Numéro d’identification invalide.',
                ]);
            }

            if (! Auth::attempt([
                'email' => $membre->user->email,
                'password' => $this->password,
            ], false)) {

                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'login' => 'Mot de passe incorrect.',
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input('login')) . '|' . $this->ip();
    }
}