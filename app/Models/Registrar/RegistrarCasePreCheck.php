<?php

namespace App\Models\Registrar;

use App\Models\Registrar\Concerns\HasRegistrarStepState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrarCasePreCheck extends Model
{
    use HasRegistrarStepState;

    protected $fillable = [
        'registrar_case_id',
        'is_enabled',
        'is_skipped',
        'submitted_at',
        'approved_at',
        'capital_amount',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_skipped' => 'boolean',
            'submitted_at' => 'date',
            'approved_at' => 'date',
        ];
    }

    public function registrarCase(): BelongsTo
    {
        return $this->belongsTo(RegistrarCase::class);
    }
}
