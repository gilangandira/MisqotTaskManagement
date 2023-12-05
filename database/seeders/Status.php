<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Status extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert([
            'name' => 'Belum Dikerjakan',
        ]);
        DB::table('status')->insert([
            'name' => 'Sedang Dikerjakan',
        ]);
        DB::table('status')->insert([
            'name' => 'Selesai Dikerjakan',
        ]);
    }
}
