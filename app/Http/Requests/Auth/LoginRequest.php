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
            'numero_identification' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $membre = Membre::where('numero_identification', $this->numero_identification)
            ->with('user')
            ->first();

        if (! $membre || ! $membre->user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'numero_identification' => 'Numéro d’identification invalide.',
            ]);
        }

        if (! Auth::attempt([
            'email' => $membre->user->email,
            'password' => $this->password,
        ], false)) { // 🔒 remember FORCÉ À FALSE

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => 'Mot de passe incorrect.',
            ]);
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
            'numero_identification' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->numero_identification).'|'.$this->ip();
    }
}
