<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Admin extends Model
{
    protected $fillable = [
        'username',
        'password',
        'auth_token',
        'token_expiry',
    ];

    // Disable created_at/updated_at
    public $timestamps = false;

    // Automatically cast token expiry to Carbon instance
    protected $casts = [
        'token_expiry' => 'datetime',
    ];

    /**
     * Automatically hash password when setting it
     */
    public function setPasswordAttribute($value)
    {
        // Only hash if it’s not already hashed
        if (!empty($value) && !Str::startsWith($value, '$2y$')) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Generate an authentication token and set expiry time
     */
    public function generateAuthToken($rememberMe = false)
    {
        $token = Str::random(64);

        $expiry = $rememberMe
            ? now()->addDays(30)   // remember me → 30 days
            : now()->addHours(8);  // normal login → 8 hours

        $this->forceFill([
            'auth_token' => $token,
            'token_expiry' => $expiry,
        ])->save();

        return $token;
    }

    /**
     * Check if current token is still valid
     */
    public function isTokenValid(): bool
    {
        return !empty($this->auth_token)
            && $this->token_expiry
            && now()->lt($this->token_expiry);
    }

    /**
     * Clear the authentication token
     */
    public function clearAuthToken(): void
    {
        $this->forceFill([
            'auth_token' => null,
            'token_expiry' => null,
        ])->save();
    }
}
