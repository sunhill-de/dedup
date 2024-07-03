<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HashTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hashtable')->insert([
            ['id'=>1,'short_hash'=>'A','long_hash'=>null,'file_path'=>'dummy'],  
            ['id'=>2,'short_hash'=>'B','long_hash'=>'BB','file_path'=>'dummy'],
        ]);
    }
}
