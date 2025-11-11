<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Link;
use App\Models\Project;
use App\Models\Task;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;

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
        'date_canceled',
        'description',
        'duration_time',
        'source',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    // Update the Opportunity duration time duration_time
    public function updateDuration()
    {
        $this->duration_time = $this->tasks->sum('duration_time');
        $this->save();
    }
}
