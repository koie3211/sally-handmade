<?php

namespace App\Models\Registrar\Concerns;

trait HasRegistrarStepState
{
    public function isComplete(): bool
    {
        if ($this->is_skipped) {
            return true;
        }

        return collect($this->completionFields())
            ->every(fn (string $field) => filled($this->{$field}));
    }

    /**
     * @return list<string>
     */
    protected function completionFields(): array
    {
        return ['approved_at'];
    }
}
