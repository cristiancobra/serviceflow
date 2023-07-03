<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'email',
        'cel_phone',
        'linkedin',
        'facebook',
        'instagram',
        'other_social_media',
        'contact_date',
        'source',
        'source_contact_channel',
        'reason_for_initial_contact',
        'comments',
    ];
}
