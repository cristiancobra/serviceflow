<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

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
}
