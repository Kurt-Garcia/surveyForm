<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Developer extends Authenticatable
{
    use Notifiable;

    protected $guard = 'developer';
    protected $table = 'developers';

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    /**
     * Check if the developer is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === true;
    }
}
