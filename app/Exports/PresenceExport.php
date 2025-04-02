<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PresenceExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private $year;
    private $month;
    private $index = 0;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        return Presence::with('warga_tels')
            ->whereYear('time_masuk', $this->year)
            ->whereMonth('time_masuk', $this->month)
            ->orderBy('time_masuk', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ["NO", "NIS", "NAMA", "KELAS", "TANGGAL MASUK", "TANGGAL KELUAR", "STATUS"];
    }

    public function map($presence): array
    {
        return [
            ++$this->index,
            $presence->warga_tels->nis ?? '-',
            $presence->warga_tels->name ?? '-',
            $presence->warga_tels->kelas ?? '-',
            $presence->time_masuk ?? '-',
            $presence->time_keluar ?? '-',
            $presence->status ?? '-',
        ];
    }

    public function title(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1)) . " " . $this->year;
    }
}