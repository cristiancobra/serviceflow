<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'journeys';

    protected $fillable = [
        'id',
        'task_id',
        'details',
        'start',
        'end',
        'duration',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }


    public function updateTaskDuration()
    {
        $task = $this->task; // Assume que há um relacionamento 'task' definido na model

        if ($task) {
            $task->duration_time = $task->journeys->sum('duration');
            $task->save();
        }
    }
}
