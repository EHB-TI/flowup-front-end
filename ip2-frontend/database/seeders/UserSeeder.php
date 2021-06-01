<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'firstName' => "license",
                'lastName' => "testc",
                'email' => "license.testc@desideriushogeschool.be",
                'password' => Hash::make("2000-12-11license.testc@desideriushogeschool.be"),
                'birthday' => "2000-12-11",
                'role' => "student",
                'study' => "Dig-x",
            ],
        ]);
    }
}
