<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public static function get_new_code()
    {
        $year_now = date('Y');
        $user = self::selectRaw("MAX(id) AS max")
            ->whereYear('created_at', $year_now)
            ->first();
        $max = ($user->max != null) ? substr($user->max, -5) : 0;
        $increment = (int)$max + 1;

        $str = str_pad($increment, 5, '0', STR_PAD_LEFT);
        $new_code = "USR/" . $year_now . "/" . $str;

        return $new_code;
    }
}
