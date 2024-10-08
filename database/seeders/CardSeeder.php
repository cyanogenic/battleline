<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            'red',
            'yellow',
            'orange',
            'green',
            'blue',
            'purple',
        ];
        
        foreach ($colors as $color) {
            for ($value=1; $value <=10; $value++) { 
                DB::table('cards')->insert([
                    'type'  => 'troop',
                    'color' => $color,
                    'value' => $value,
                ]);
            }
        }
    }
}
