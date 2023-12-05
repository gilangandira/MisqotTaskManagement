<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobuser extends Model
{
    use HasFactory;
    protected $table = 'jobuser';
    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }
    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
