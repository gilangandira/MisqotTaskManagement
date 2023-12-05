<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Vendor extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vendor')->insert([
            'name' => 'LHG 5HP',
            'brand' => 'Mikrotik',
            'cpu' => 'LHG 5HP',
            'cpu_core' => 'LHG 5HP',
            'ram' => 'LHG 5HP',
            'lan_ports' => 'LHG 5HP',
            'lan_speed' => 'LHG 5HP',
            'wireless_standards' => 'LHG 5HP',
            'guest_network' => 'LHG 5HP',
            'power' => 'LHG 5HP',
        ]);
        DB::table('vendor')->insert([
            'name' => 'LHG 5HP',
            'brand' => 'Mikrotik',
            'cpu' => 'LHG 5HP',
            'cpu_core' => 'LHG 5HP',
            'ram' => 'LHG 5HP',
            'lan_ports' => 'LHG 5HP',
            'lan_speed' => 'LHG 5HP',
            'wireless_standards' => 'LHG 5HP',
            'guest_network' => 'LHG 5HP',
            'power' => 'LHG 5HP',
        ]);
        DB::table('vendor')->insert([
            'name' => 'LHG 5HP',
            'brand' => 'Mikrotik',
            'cpu' => 'LHG 5HP',
            'cpu_core' => 'LHG 5HP',
            'ram' => 'LHG 5HP',
            'lan_ports' => 'LHG 5HP',
            'lan_speed' => 'LHG 5HP',
            'wireless_standards' => 'LHG 5HP',
            'guest_network' => 'LHG 5HP',
            'power' => 'LHG 5HP',
        ]);

    }
}
