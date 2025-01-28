<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'account_id',
        'project_id',
        'user_id',
        'task_id',
        'opportunity_id',
        'url',
        'title',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }
}
