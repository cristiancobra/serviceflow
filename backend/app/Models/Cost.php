<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

class Cost extends Model
{
    use HasFactory, SoftDeletes, BelongsToAccount;

    protected $fillable = [
        'name',
        'price',
        'company_id',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_costs')
            ->withPivot('quantity', 'price', 'total_price')
            ->withTimestamps();
    }
}
