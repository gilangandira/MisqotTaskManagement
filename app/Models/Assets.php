<?php

namespace App\Models;

use App\Models\SLA;
use App\Models\Task;
use App\Models\User;
use App\Models\Status;
use App\Models\CategoryAssets;
use App\Models\ConditionAssets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assets extends Model
{
    use Notifiable;
    use HasFactory;
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(CategoryAssets::class);
    }

    public function condition()
    {
        return $this->belongsTo(ConditionAssets::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function task()
    {
        return $this->hasMany(Task::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function sla()
    {
        return $this->belongsTo(SLA::class);
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

}