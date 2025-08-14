<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\WargaTels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $rowsProcessed = 0;
    private $rowsUpdated = 0;
    private $rowsInserted = 0;
    private $photosProcessed = 0;
    private $photoMap;
    
    public function __construct(array $photoMap = [])
    {
        $this->photoMap = $photoMap;
    }
    
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Pastikan field utama NIS, Nama, Kelas terisi
            if (empty($row['nis']) || empty($row['nama']) || empty($row['kelas'])) {
                continue;
            }
            
            // Jika alamat kosong, isi dengan strip (-)
            $alamat = trim($row['alamat'] ?? '');
            if ($alamat === '') {
                $alamat = '-';
            }
            
            // Default foto_profile
            $fotoProfile = 'default.jpg';
            
            // Cek jika ada foto di Excel
            if (!empty($row['foto_profile'])) {
                $photoName = $row['foto_profile'];
                
                // Cek di ZIP upload
                if (isset($this->photoMap[$photoName])) {
                    $fotoProfile = $photoName;
                    $this->photosProcessed++;
                }
                // Cek di S3
                elseif (Storage::disk('s3')->exists('profile/' . $photoName)) {
                    $fotoProfile = $photoName;
                }
            }
            
            // Cari siswa berdasarkan NIS
            $existingStudent = WargaTels::where('nis', $row['nis'])->first();
            
            if ($existingStudent) {
                // Update data
                $existingStudent->update([
                    'name' => $row['nama'],
                    'kelas' => $row['kelas'],
                    'alamat' => $alamat,
                    'foto_profile' => $fotoProfile
                ]);
                $this->rowsUpdated++;
            } else {
                // Insert data baru
                WargaTels::create([
                    'nis' => $row['nis'],
                    'name' => $row['nama'],
                    'kelas' => $row['kelas'],
                    'alamat' => $alamat,
                    'foto_profile' => $fotoProfile
                ]);
                $this->rowsInserted++;
            }
            
            $this->rowsProcessed++;
        }
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|numeric',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'foto_profile' => 'nullable|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nis.required' => 'NIS wajib diisi',
            'nis.numeric' => 'NIS harus berupa angka',
            'nama.required' => 'Nama wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
        ];
    }
    
    public function getRowsProcessed()
    {
        return $this->rowsProcessed;
    }
    
    public function getRowsUpdated()
    {
        return $this->rowsUpdated;
    }
    
    public function getRowsInserted()
    {
        return $this->rowsInserted;
    }
    
    public function getPhotosProcessed()
    {
        return $this->photosProcessed;
    }
}
