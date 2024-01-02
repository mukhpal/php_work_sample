<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'otp',
        'device_token',
        'email_verified',
        'folder',
        'profile_pic',
        'social_id',
        'social_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'device_token',
        'email_verified',
        'folder',
        'social_id',
        'social_type'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $sortable = ['first_name',
                        'email',
                        'email_verified'
                    ];


    /**
     * Get the recordings for the user.
     */
    public function recordings()
    {
        return $this->hasMany(Recordings::class);
    }
}
