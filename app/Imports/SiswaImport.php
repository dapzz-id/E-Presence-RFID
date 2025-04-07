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
            // Check if all required fields have values
            if (empty($row['nis']) || empty($row['nama']) || empty($row['kelas']) || empty($row['alamat'])) {
                continue; // Skip rows with empty required fields
            }
            
            // Set default foto_profile if not provided
            $fotoProfile = 'default.jpg';
            
            // Check if photo exists in the photo map or already in storage
            if (!empty($row['foto_profile'])) {
                $photoName = $row['foto_profile'];
                
                // Check if photo exists in the uploaded ZIP
                if (isset($this->photoMap[$photoName])) {
                    $fotoProfile = $photoName;
                    $this->photosProcessed++;
                }
                // Check if photo already exists in storage
                elseif (Storage::disk('public')->exists('profile/' . $photoName)) {
                    $fotoProfile = $photoName;
                }
            }
            
            // Check if student with this NIS already exists
            $existingStudent = WargaTels::where('nis', $row['nis'])->first();
            
            if ($existingStudent) {
                // Update existing student (only specific fields)
                $existingStudent->update([
                    'name' => $row['nama'],
                    'kelas' => $row['kelas'],
                    'alamat' => $row['alamat'],
                    'foto_profile' => $fotoProfile
                ]);
                
                $this->rowsUpdated++;
            } else {
                // Create new student
                WargaTels::create([
                    'nis' => $row['nis'],
                    'name' => $row['nama'],
                    'kelas' => $row['kelas'],
                    'alamat' => $row['alamat'],
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
            'alamat' => 'required|string',
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
            'alamat.required' => 'Alamat wajib diisi',
        ];
    }
    
    // Getter methods for statistics
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