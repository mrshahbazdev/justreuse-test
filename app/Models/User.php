<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail{

    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_blocked',
        'google_id',
        'facebook_id',
        'is_fb_login',
        'deleted_at',
        'last_chat_seen',
        'current_chat_status',
        'api_token',
        'profile_photo_path',
        'phone',
        'websocket_id','preferred_language',
		'email_verified_at',
        'preferred_currency',
        'otp', // Yeh line shamil ki gayi hai
        'otp_expires_at', // Yeh line shamil ki gayi hai
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime', // Yeh line shamil ki gayi hai
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];
    //UUID begin
    public $incrementing = false;
    protected $keyType = 'string';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    //UUID end
    public function user_profile() {
        return $this->hasOne(User_profile::class);
    }

    public static function blocked_users() {
        $get = User::where('is_blocked', '1')->get()->toArray();
        $blocked_ids = [];
        foreach ($get as $r) {
            $blocked_ids[] = $r["id"];
        }
        return $blocked_ids;
    }

    public static function isDemoUser()
    {
        $user = auth()->user();
        if(!$user) return ["result" => false, "message" => ""];
        $result =  $user->hasRole('Admin') ? true : false;
        
        $result_arr = ["result"=>$result,"message"=>"Not allowed the operation"];
        return $result_arr;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
}

