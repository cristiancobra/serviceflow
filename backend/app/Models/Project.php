<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'id',
        'account_id',
        'opportunity_id',
        'user_id',
        'contact_id',
        'company_id',
        'goal_id',
        'name',
        'category',
        'date_start',
        'date_due',
        'date_conclusion',
        'description',
        'status',
        'priority',
    ];

    public static function getStatus()
    {
        return [
            'fazer',
            'aguardar',
            'feito',
            'fazendo',
            'cancelado',
        ];
    }

    // relationships
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
