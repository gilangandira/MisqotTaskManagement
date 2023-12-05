<?php

namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SLA extends Model
{
    use HasFactory;
    protected $table = 'sla';
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
