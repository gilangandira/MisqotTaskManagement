<?php

namespace App\Models;

use App\Models\SLA;
use App\Models\User;
use App\Models\Assets;
use App\Models\Status;
use App\Models\Comment;
use App\Models\TimeTracker;
use App\Models\ConditionAssets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;

    protected $table = 'tasks';
    protected $guarded = [
        'id'
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'jobuser');
    }


    public function comment()
    {
        return $this->belongsToMany(Comment::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function assets()
    {
        return $this->belongsTo(Assets::class);
    }
    public function sla()
    {
        return $this->belongsTo(SLA::class);
    }

    public function condition()
    {
        return $this->belongsTo(ConditionAssets::class);
    }
    public function timetracker()
    {
        return $this->belongsTo(TimeTracker::class);
    }

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
}