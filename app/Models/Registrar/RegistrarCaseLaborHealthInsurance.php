<?php

namespace App\Models\Registrar;

use App\Models\Registrar\Concerns\HasRegistrarStepState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrarCaseLaborHealthInsurance extends Model
{
    use HasRegistrarStepState;

    protected $fillable = [
        'registrar_case_id',
        'is_enabled',
        'is_skipped',
        'labor_submitted_at',
        'health_submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_skipped' => 'boolean',
            'labor_submitted_at' => 'date',
            'health_submitted_at' => 'date',
        ];
    }

    public function registrarCase(): BelongsTo
    {
        return $this->belongsTo(RegistrarCase::class);
    }

    protected function completionFields(): array
    {
        return ['labor_submitted_at', 'health_submitted_at'];
    }
}
