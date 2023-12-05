<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Category extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categoryassets')->insert([
            'name' => 'Router',
        ]);
        DB::table('categoryassets')->insert([
            'name' => 'Switch',
        ]);
        DB::table('categoryassets')->insert([
            'name' => 'Radio',
        ]);
    }
}
