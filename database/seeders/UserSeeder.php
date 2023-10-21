<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        //Faker is a package generates random names
        $faker = FakerFactory::create();

        $emailExtensions = ['clicko.es', 'gmail.com', 'outlook.com', 'yahoo.com'];

        for ($i = 0; $i < 20; $i++) {
            // Email random selection with array_rand func
            $randomExtension = $emailExtensions[array_rand($emailExtensions)];
            $randomName = $faker->firstName;
            $email = $randomName . '@' . $randomExtension;

            \App\Models\User::create([
                'name' => $randomName,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
