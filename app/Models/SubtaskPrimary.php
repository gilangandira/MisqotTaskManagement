<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubtaskPrimary extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];


    public function subTask()
    {
        return $this->hasMany(Task::class);
    }

}