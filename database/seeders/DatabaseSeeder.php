<?php

namespace Database\Seeders;

use App\Models\AdminAccount;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\WargaTels;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // WargaTels::factory(18)->create();
        AdminAccount::factory(1)->create();
        SuperAdmin::factory(2)->create();

        $wargaTels1 = WargaTels::factory()->create([
            'nis' => '232410012',
            'name' => "KADAVI RADITYA ALVINO",
            'kelas' => "XI RPL 2",
            'alamat' => "Tridaya Sakti, Taman Puri Cendana, Grand Mawar, Blok A4 no 10",
            'foto_profile' => "Kadavi Raditya Alvino.jpg"
        ]);
        
        $wargaTels2 = WargaTels::factory()->create([
            'nis' => '232410008',
            'name' => "DIRTANDRA TAUFIQ AL-RAFI'I",
            'kelas' => "XI RPL 1",
            'alamat' => "-",
            'foto_profile' => "Dirtandra Putra Taufiq Al-Rafii.jpg"
        ]);
        
        User::factory()->create([
            'nis' => $wargaTels1->nis,
            'username' => 'kadaviradityaa',
            'email' => 'kadaviradityaa@gmail.com',
            'password' => Hash::make('kadavi007'),
            'rfid_id' => "0262144736"
        ]);
        
        User::factory()->create([
            'nis' => $wargaTels2->nis,
            'username' => 'dirtandraa',
            'email' => 'radityaakadavi@gmail.com',
            'password' => Hash::make('kadavi007'),
            'rfid_id' => "2399694825"
        ]);
    }
}
