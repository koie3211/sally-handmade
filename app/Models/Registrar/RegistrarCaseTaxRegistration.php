<?php

namespace App\Models\Registrar;

use App\Models\Registrar\Concerns\HasRegistrarStepState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrarCaseTaxRegistration extends Model
{
    use HasRegistrarStepState;

    protected $fillable = [
        'registrar_case_id',
        'is_enabled',
        'is_skipped',
        'submitted_at',
        'signed_at',
        'approved_at',
        'opened_at',
        'tax_officer_name',
        'tax_officer_phone',
        'invoice_purchase_certificate_received_at',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_skipped' => 'boolean',
            'submitted_at' => 'date',
            'signed_at' => 'date',
            'approved_at' => 'date',
            'opened_at' => 'date',
            'invoice_purchase_certificate_received_at' => 'date',
        ];
    }

    public function registrarCase(): BelongsTo
    {
        return $this->belongsTo(RegistrarCase::class);
    }
}
