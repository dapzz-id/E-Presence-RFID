<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Example data row (empty)
        return [
            ['12345', 'Nama Siswa', 'X RPL 1', 'Alamat Siswa (Strip (-) Jika kosong)', 'foto.jpg (Sesuaikan dengan Photo file name)'] // Example row
        ];
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama',
            'Kelas',
            'Alamat',
            'Foto Profile'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => ['font' => ['bold' => true, 'size' => 12]],
            
            // Add notes about required fields
            'A1:E1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E2EFDA']]],
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 15, // NIS
            'B' => 30, // Nama
            'C' => 15, // Kelas
            'D' => 40, // Alamat
            'E' => 20, // Foto Profile
        ];
    }
}