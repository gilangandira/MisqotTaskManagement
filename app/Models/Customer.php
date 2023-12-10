<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customer';
    // protected $fillable = [
    //     'customers_name',
    //     'ppoe_username',
    //     'ppoe_password',
    //     'ip_client',
    //     'ap_ssid',
    //     'channel_frequensy',
    //     'bandwith',
    //     'subscription_fee',
    //     'location',
    //     'start_dates',
    //     'image'
    // ];

    protected $guarded = ['id'];
    protected $hidden = [];

    public function assets()
    {
        return $this->hasMany(Assets::class);
    }

}