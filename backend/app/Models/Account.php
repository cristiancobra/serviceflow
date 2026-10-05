<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
    'zip_code',
    'ibge_city_code',
    'cnpj',
    'is_mei',
    'mei_annual_limit',
    'inscricao_municipal',
    'nfse_environment',
    'nfse_default_service_code',
    'nfse_dps_series',
    'nfse_next_dps_number',
    'pix_key',
    'theme_preference',
    'logo',
    'subscription_status',
    'expiration_date',
    'is_active',
    'deleted_at',
    'created_at',
    'updated_at',
];

// Certificado A1 da NFS-e: gravado só pelo endpoint de upload, nunca exposto na API
protected $hidden = [
    'nfse_certificate_path',
    'nfse_certificate_password',
];

protected $casts = [
    'nfse_certificate_password' => 'encrypted',
    'nfse_certificate_expires_at' => 'datetime',
];

public const NFSE_ENVIRONMENTS = ['restricted', 'production'];

/**
 * Get the departments for this account
 */
public function departments()
{
    return $this->hasMany(Department::class);
}

/**
 * Reserva o próximo número de DPS da série atual.
 * O lock na linha da conta garante que duas emissões simultâneas nunca recebam o mesmo número.
 */
public function reserveNextDpsNumber(): int
{
    return DB::transaction(function () {
        $account = self::whereKey($this->id)->lockForUpdate()->first();
        $number = $account->nfse_next_dps_number;

        $account->nfse_next_dps_number = $number + 1;
        $account->save();

        $this->nfse_next_dps_number = $account->nfse_next_dps_number;

        return $number;
    });
}

public function hasNfseCertificate(): bool
{
    return !empty($this->nfse_certificate_path);
}

}