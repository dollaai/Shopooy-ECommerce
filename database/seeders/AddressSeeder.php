<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $type_address = ['home', 'office'];
        $cities = City::with('province')->get();
        $districts = Factory::create()->city;
        $data = [
            [
                'user_id' => 1,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'is_default' => true,
                'receiver_name' => $users->random()->name,
                'receiver_phone' => Factory::create()->phoneNumber,
                'city_id' => $cities->random()->id,
                'district' => $districts,
                'postal_code' => '12345',
                'detail_address' => Factory::create()->address,
                'address_note' => Factory::create()->sentence,
                'type' => $type_address[array_rand($type_address)]
            ],
            [
                'user_id' => 2,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'is_default' => true,
                'receiver_name' => $users->random()->name,
                'receiver_phone' => Factory::create()->phoneNumber,
                'city_id' => $cities->random()->id,
                'district' => $districts,
                'postal_code' => '12345',
                'detail_address' => Factory::create()->address,
                'address_note' => Factory::create()->sentence,
                'type' => $type_address[array_rand($type_address)] 
            ],
        ];

        foreach ($data as $address) {
            \App\Models\Address::create($address);
        }
    }
}
