<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('files')->insert([
            [
                'id'=>1,
                'hash'=>'6dcd4ce23d88e2ee9568ba546c007c63d9131c1b',
                'size'=>1,
                'mime'=>2,
                'dir'=>'/sample/dir/',
                'name'=>'A',
                'extension'=>'txt',
                'state'=>'regular'                
            ],  
        ]);
    }
}
