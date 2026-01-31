<?php

namespace App\Services;

use App\Models\Agence;
use App\Models\User;

class AgenceContext
{
    public static function get()
    {
        if (session()->has('agence_active_id')) {
            return Agence::find(session('agence_active_id'));
        }

        return auth()->user()->agent->agence ?? null;
    }

    public static function set(int $agenceId): void
    {
        session(['agence_active_id' => $agenceId]);
    }
}
