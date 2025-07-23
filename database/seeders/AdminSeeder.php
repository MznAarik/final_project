<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert into countries
        $countryId = DB::table('countries')->insertGetId([
            'name' => 'nepal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert into provinces
        $provinceId = DB::table('provinces')->insertGetId([
            'country_id' => $countryId,
            'name' => 'bagmati province',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert into districts
        $districtId = DB::table('districts')->insertGetId([
            'province_id' => $provinceId,
            'name' => 'lalitpur',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert admin user
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'super admin',
            'email' => 'mhrznaaa.980@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123123'), // Always hash passwords
            'gender' => 'male',
            'phoneno' => '9800000000',
            'address' => 'patan',
            'district_id' => $districtId,
            'province_id' => $provinceId,
            'country_id' => $countryId,
            'date_of_birth' => '1990-01-01',
            'role' => 'admin',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => 1,
            'updated_by' => 1,
            'delete_flag' => 0,
        ]);
    }
}
