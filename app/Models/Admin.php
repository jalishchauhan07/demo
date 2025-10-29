<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Admin extends Model
{
    protected $fillable = [
        'username',
        'password',
        'auth_token',
        'token_expiry',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'token_expiry' => 'datetime',
    ];

    /**
     * Generate and save authentication token
     */
    public function generateAuthToken(bool $rememberMe = false): string
    {
        $token = Str::random(64);
        $hours = $rememberMe ? 720 : 24; // 30 days or 24 hours
        
        $this->auth_token = $token;
        $this->token_expiry = now()->addHours($hours);
        $this->save();
        
        return $token;
    }

    /**
     * Check if token is valid
     */
    public function isTokenValid(): bool
    {
        return $this->auth_token !== null 
            && $this->token_expiry !== null 
            && $this->token_expiry->isFuture();
    }

    /**
     * Clear authentication token
     */
    public function clearAuthToken(): void
    {
        $this->auth_token = null;
        $this->token_expiry = null;
        $this->save();
    }
}