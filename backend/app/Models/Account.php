<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory;
    use SoftDeletes;


protected $fillable = [
    'name',
    'slug',
    'owner_id',
    'email',
    'phone',
    'address',
    'address_city',
    'cnpj',
    'inscricao_municipal',
    'logo',
    'subscription_status',
    'expiration_date',
    'is_active',
    'deleted_at',
    'created_at',
    'updated_at',
]; 

}