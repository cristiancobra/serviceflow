<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

	protected $table = 'tasks';
	
    protected $fillable = [
        'id',
        'user_id',
        'company_id',
        'account_id',
        'contact_id',
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
        'status',
        'priority',
        'start_time',
        'end_time',
        'duration',
        'points',
        'type',
    ];
	
		
	public static function getStatus() {
        return [
            'fazer',
            'aguardar',
            'feito',
            'fazendo',
            'cancelado',
        ];
	}
}
