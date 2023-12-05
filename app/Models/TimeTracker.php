<?php

namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeTracker extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'timetracker';
   
    public function task()
    {
        return $this->hasMany(Task::class);
    }
}
