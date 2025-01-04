<?php

namespace App\Traits;

use App\Models\VerificationCode;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVerificationCodes
{
    /**
     * Get the verification codes for the model.
     */
    public function verificationCodes(): MorphMany
    {
        return $this->morphMany(VerificationCode::class, 'verifiable');
    }
}