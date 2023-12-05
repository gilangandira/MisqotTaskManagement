<?php

namespace App\Models;

use App\Models\Assets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryAssets extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'categoryassets';
    public function assets()
    {
        return $this->hasMany(Assets::class);
    }
}