<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SLA extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sla')->insert([
            'name' => 'Perbaikan Alat',
            'waktu' => '1'
        ]);
        DB::table('sla')->insert([
            'name' => 'Pergantian Alat',
            'waktu' => '2'
        ]);
        DB::table('sla')->insert([
            'name' => 'Instalasi Baru',
            'waktu' => '1'
        ]);
    }
}
