<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

class Journey extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;

    protected $table = 'journeys';

    protected $fillable = [
        'id',
        'user_id',
        'task_id',
        'details',
        'start',
        'end',
        'duration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Update the Opportunity duration time duration_time
    private function updateOpportunityDuration()
    {
        $task = $this->task;

        if ($task) {
            $task->updateOpportunityDuration();
        }
    }

    // Update the task duration time duration_time

    public function updateAssociatedTaskDuration()
    {
        $task = $this->task; // Assume que há um relacionamento 'task' definido na model

        if ($task) {
            $task->duration_time = $task->journeys->sum('duration');
            $task->save();
            $task->updateAssociatedOpportunityDuration();
        }
    }

    public static function getOpenJourney($userId)
    {
        return self::where('user_id', $userId)
            ->whereNotNull('start')
            ->whereNull('end')
            ->with(['task', 'task.opportunity', 'task.project'])
            ->first();
    }
}
