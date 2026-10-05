<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToAccount;

class Department extends Model
{
    use HasFactory, BelongsToAccount;

    protected $fillable = [
        'account_id',
        'name',
        'slug',
        'color',
        'icon',
        'description',
        'active',
        'order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the tasks for this department
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the account that owns the department
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Departamento Financeiro da conta, que recebe as tarefas "Pagar: ...".
     * Filtra a conta explicitamente para funcionar sem usuário logado (comandos agendados).
     */
    public static function financeiroIdFor($accountId)
    {
        return static::withoutGlobalScopes()
            ->where('account_id', $accountId)
            ->where(function ($query) {
                $query->where('slug', 'financeiro')->orWhere('name', 'like', '%financeiro%');
            })
            ->value('id');
    }
}
