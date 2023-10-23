<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;


    protected $table = 'companies';

    protected $fillable = [
        'account_id',
        'business_name',
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
