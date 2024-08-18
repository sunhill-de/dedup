<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mimes')->insert([
            ['id'=>1,'main'=>'Audio','sub'=>'Mpeg'],  
            ['id'=>2,'main'=>'Application','sub'=>'Test'],
        ]);
    }
}
