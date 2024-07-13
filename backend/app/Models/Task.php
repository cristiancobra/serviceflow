<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

	protected $table = 'tasks';
	
    protected $fillable = [
        'id',
        'user_id',
        'company_id',
        'account_id',
        'contact_id',
        'opportunity_id',
        'project_id',
        'stage_id',
        'date_entered',
        'created_by',
        'name',
        'department',
        'description',
        'date_due',
        'date_start',
        'date_conclusion',
        'duration_days',
        'duration_time',
        'status',
        'priority',
        'points',
        'type',
    ];

     /**
     * Get the journeys from task
     */
    public function journeys()
    {
        return $this->hasMany(Journey::class);
    }

    /**
     * Get the opportunity from task
     */
    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Get the project from task
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
	
		
	public static function getStatus() {
        return [
            'to-do',
            'wait',
            'done',
            'doing',
            'canceled',
        ];
	}
}
