<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {

	use HasFactory;
	
	protected $table = 'projects';

    protected $fillable = [
        'id',
        'account_id',
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
        'trash',
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
