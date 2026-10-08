<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToAccount;

class DueDateChange extends Model
{
    use BelongsToAccount;

    protected $table = 'due_date_changes';

    protected $fillable = [
        'user_id',
        'previous_date',
        'new_date',
        'reason',
        'note',
    ];

    /**
     * Motivos de alteração do prazo. "other" exige uma observação.
     */
    public const REASONS = [
        'reprioritized' => 'Repriorizado',
        'waiting_client' => 'Aguardando cliente',
        'underestimated' => 'Estimativa errada',
        'internal_dependency' => 'Dependência interna',
        'other' => 'Outro',
    ];

    public function changeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function reasonLabel(?string $reason): ?string
    {
        return $reason ? (self::REASONS[$reason] ?? $reason) : null;
    }
}
