<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Import Carbon for timestamps

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // --- Truncate tables in correct order to avoid foreign key issues ---
        DB::table('districts')->truncate();
        DB::table('provinces')->truncate();
        DB::table('countries')->truncate();

        // --- 1. Seed Countries Table ---
        $this->command->info('Seeding Countries...');
        DB::table('countries')->insert([
            [
                'id' => 1,
                'name' => 'Nepal',
                'user_id' => null, // As per your schema, user_id is nullable
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
        $this->command->info('Countries seeded successfully!');

        // --- 2. Seed Provinces Table ---
        $this->command->info('Seeding Provinces...');
        $provinces = [
            ['id' => 1, 'name' => 'Koshi Province', 'country_id' => 1, 'user_id' => 1, 'number' => '1', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'name' => 'Madhesh Province', 'country_id' => 1, 'user_id' => 1, 'number' => '2', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'name' => 'Bagmati Province', 'country_id' => 1, 'user_id' => 1, 'number' => '3', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'name' => 'Gandaki Province', 'country_id' => 1, 'user_id' => 1, 'number' => '4', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 5, 'name' => 'Lumbini Province', 'country_id' => 1, 'user_id' => 1, 'number' => '5', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 6, 'name' => 'Karnali Province', 'country_id' => 1, 'user_id' => 1, 'number' => '6', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 7, 'name' => 'Sudurpashchim Province', 'country_id' => 1, 'user_id' => 1, 'number' => '7', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];
        DB::table('provinces')->insert($provinces);
        $this->command->info('Provinces seeded successfully!');

        // --- 3. Seed Districts Table ---
        $this->command->info('Seeding Districts...');
        $districts_by_province = [
            1 => [ // Koshi Province (ID 1)
                "Bhojpur", "Dhankuta", "Ilam", "Jhapa", "Khotang", "Morang",
                "Okhaldhunga", "Panchthar", "Sankhuwasabha", "Solukhumbu",
                "Sunsari", "Taplejung", "Terhathum", "Udayapur"
            ],
            2 => [ // Madhesh Province (ID 2)
                "Bara", "Dhanusha", "Mahottari", "Parsa", "Rautahat",
                "Saptari", "Sarlahi", "Siraha"
            ],
            3 => [ // Bagmati Province (ID 3)
                "Bhaktapur", "Chitwan", "Dhading", "Dolakha", "Kathmandu",
                "Kavrepalanchok", "Lalitpur", "Makwanpur", "Nuwakot",
                "Ramechhap", "Rasuwa", "Sindhuli", "Sindhupalchok"
            ],
            4 => [ // Gandaki Province (ID 4)
                "Baglung", "Gorkha", "Kaski", "Lamjung", "Manang", "Mustang",
                "Myagdi", "Nawalpur (East Nawalparasi)", "Parbat", "Syangja", "Tanahun"
            ],
            5 => [ // Lumbini Province (ID 5)
                "Arghakhanchi", "Banke", "Bardiya", "Dang", "Gulmi",
                "Kapilvastu", "Parasi (West Nawalparasi)", "Palpa", "Pyuthan",
                "Rolpa", "Rukum (Eastern Part)", "Rupandehi"
            ],
            6 => [ // Karnali Province (ID 6)
                "Dailekh", "Dolpa", "Humla", "Jajarkot", "Jumla",
                "Kalikot", "Mugu", "Salyan", "Surkhet", "Rukum (Western Part)"
            ],
            7 => [ // Sudurpashchim Province (ID 7)
                "Achham", "Baitadi", "Bajhang", "Bajura", "Dadeldhura",
                "Darchula", "Doti", "Kailali", "Kanchanpur"
            ]
        ];

        $districts_to_insert = [];
        $district_id_counter = 1;

        foreach ($districts_by_province as $province_id => $districts_list) {
            foreach ($districts_list as $district_name) {
                $districts_to_insert[] = [
                    'id' => $district_id_counter,
                    'name' => $district_name,
                    'province_id' => $province_id,
                    'user_id' => 1, // As per your schema, user_id is NOT nullable, so a default value (e.g., 1) is provided.
                    'headquarters' => null, // As per your schema, headquarters is nullable
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $district_id_counter++;
            }
        }
        DB::table('districts')->insert($districts_to_insert);
        $this->command->info('Districts seeded successfully!');
    }
}
