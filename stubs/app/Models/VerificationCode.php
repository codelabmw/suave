<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    /** @use HasFactory<\Database\Factories\VerificationCodeFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'expires_at',
        'verified_at',
    ];

    /**
     * Get the owning verifiable model.
     */
    public function verifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determine if the verification code has expired.
     */
    public function hasExpired(): bool
    {
        return now()->gte($this->expires_at);
    }
}
