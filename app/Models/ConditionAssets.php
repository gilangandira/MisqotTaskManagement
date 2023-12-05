<?php

namespace App\Models;

use App\Models\Assets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConditionAssets extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'conditionassets';
    public function assets()
    {
        return $this->hasMany(Assets::class);
    }
    public function task()
    {
        return $this->hasMany(Task::class);
    }
}