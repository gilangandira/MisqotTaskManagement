<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Condition extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conditionassets')->insert([
            'name' => 'Normal',
        ]);
        DB::table('conditionassets')->insert([
            'name' => 'Rusak',
        ]);
        DB::table('conditionassets')->insert([
            'name' => 'Hilang',
        ]);
    }
}
