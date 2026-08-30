<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToAccount;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;
    use BelongsToAccount;


    protected $table = 'companies';

    protected $fillable = [
        'account_id',
        'business_name',
        'photo',
        'legal_name',
        'cnpj',
        'email',
        'phone',
        'cel_phone',
        'facebook',
        'linkedin',
        'address',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'zip_code',
    ];

    public function leads()
    {
        return $this->belongsToMany(Lead::class);
    }
}
