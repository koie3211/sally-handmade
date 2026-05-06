<?php

namespace App\Models\Registrar;

use App\Enums\Registrar\CaseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RegistrarCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'accountant',
        'customer_code',
        'customer_short_name',
        'tax_id_number',
        'contact_name',
        'contact_phone',
        'service_items',
        'service_item_other',
        'status',
        'submission_agency',
        'uses_e_invoice',
        'e_invoice_note',
    ];

    protected function casts(): array
    {
        return [
            'service_items' => 'array',
            'status' => CaseStatus::class,
            'uses_e_invoice' => 'boolean',
        ];
    }

    public function preCheck(): HasOne
    {
        return $this->hasOne(RegistrarCasePreCheck::class);
    }

    public function businessRegistration(): HasOne
    {
        return $this->hasOne(RegistrarCaseBusinessRegistration::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(RegistrarCaseCertificate::class);
    }

    public function taxRegistration(): HasOne
    {
        return $this->hasOne(RegistrarCaseTaxRegistration::class);
    }

    public function permit(): HasOne
    {
        return $this->hasOne(RegistrarCasePermit::class);
    }

    public function tdccReport(): HasOne
    {
        return $this->hasOne(RegistrarCaseTdccReport::class);
    }

    public function laborHealthInsurance(): HasOne
    {
        return $this->hasOne(RegistrarCaseLaborHealthInsurance::class);
    }

    public function importExportRegistration(): HasOne
    {
        return $this->hasOne(RegistrarCaseImportExportRegistration::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(RegistrarCasePayment::class);
    }
}
