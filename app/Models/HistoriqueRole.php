<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;

class HistoriqueRole extends Model
{
    use Blameable;

    protected $fillable = [
        'user_id',
        'ancien_role',
        'nouveau_role',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ancienRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'ancien_role');
    }

    public function nouveauRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'nouveau_role');
    }

    public static function logRoleChange(int $userId, ?int $ancienRoleId, int $nouveauRoleId): self
    {
        return self::create([
            'user_id'      => $userId,
            'ancien_role'  => $ancienRoleId,
            'nouveau_role' => $nouveauRoleId,
        ]);
    }
}
