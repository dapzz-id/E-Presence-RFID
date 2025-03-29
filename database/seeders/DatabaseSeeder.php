<?php

namespace Database\Seeders;

use App\Models\AdminAccount;
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
        WargaTels::factory(18)->create();
        AdminAccount::factory(2)->create();

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
        
        function getKelasJurusan($kelasString) {
            $kelasArray = explode(' ', $kelasString);
            return [
                'kelas' => $kelasArray[0],
                'jurusan' => $kelasArray[1],
                'angka_kelas' => $kelasArray[2]
            ];
        }
        
        $data1 = getKelasJurusan($wargaTels1->kelas);
        $data2 = getKelasJurusan($wargaTels2->kelas);
        
        User::factory()->create([
            'nis' => $wargaTels1->nis,
            'username' => 'kadaviradityaa',
            'name' => $wargaTels1->name,
            'password' => Hash::make('kadavi007'),
            'kelas' => $data1['kelas'],
            'jurusan' => $data1['jurusan'],
            'angka_kelas' => $data1['angka_kelas'],
            'rfid_id' => "0262144736"
        ]);
        
        User::factory()->create([
            'nis' => $wargaTels2->nis,
            'username' => 'dirtandraa',
            'name' => $wargaTels2->name,
            'password' => Hash::make('kadavi007'),
            'kelas' => $data2['kelas'],
            'jurusan' => $data2['jurusan'],
            'angka_kelas' => $data2['angka_kelas'],
            'rfid_id' => "2399694825"
        ]);
    }
}
