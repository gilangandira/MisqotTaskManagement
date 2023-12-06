<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Performance extends Model {
    use HasFactory;
    protected $table = 'performance';
    protected $guarded = ['id'];
    public function tasks() {
        return $this->hasMany(User::class);
    }
}
