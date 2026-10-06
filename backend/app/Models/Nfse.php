<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToAccount;

class Nfse extends Model
{
    use BelongsToAccount;

    public const STATUS_PENDING = 'pending';
    public const STATUS_AUTHORIZED = 'authorized';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'nfses';

    protected $fillable = [
        'account_id',
        'invoice_id',
        'user_id',
        'is_manual',
        'environment',
        'dps_series',
        'dps_number',
        'status',
        'service_code',
        'description',
        'amount',
        'competence_date',
        'access_key',
        'nfse_number',
        'issued_at',
        'error_message',
        'cancelled_at',
        'cancellation_reason',
        'xml_path',
        'pdf_path',
    ];

    protected $hidden = [
        'xml_path',
        'pdf_path',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
        'amount' => 'decimal:2',
        'competence_date' => 'date',
        'issued_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
