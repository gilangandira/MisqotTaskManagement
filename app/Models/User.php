<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    // protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    //     'role',
    //     'fcm_token'
    // ];
    protected $guarded = [
        'id'
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
    ];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if($model->getKey() == null) {
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());

            }
        });

    }

    public function tasks() {
        return $this->belongsToMany(Task::class, 'jobuser');
    }
    public function performance() {
        return $this->belongsToMany(Performance::class);
    }
    public function assets() {
        return $this->hasMany(Assets::class);
    }

    /**
     * Specifies the user's FCM token
     * 
     * @return string|array
     */

    public function routeNotificationForFCM() {
        return $this->fcm_token;
    }

}