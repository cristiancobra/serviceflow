<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'user_id',
        'lead_id',
        'company_id',
        'name',
        'category',
        'date_start',
        'date_due',
        'date_conclusion',
        'description',
        'source',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
