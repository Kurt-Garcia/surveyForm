<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'contact_number',
        'sbu_id',
        'site_id',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    /**
     * Get the site that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
    
    /**
     * Get the SBU that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sbu(): BelongsTo
    {
        return $this->belongsTo(Sbu::class);
    }

    /**
     * Get the admin who created this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
