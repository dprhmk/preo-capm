<?php

namespace Database\Seeders;

use App\Models\Member;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
	    $this->call([
		    UserSeeder::class,
	    ]);

//        Member::factory(100)->create();
    }
}
