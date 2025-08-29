<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLocation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'ip_address',
        'user_agent',
        'login_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'login_at' => 'datetime',
    ];

    /**
     * Get the user that owns this login location.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
