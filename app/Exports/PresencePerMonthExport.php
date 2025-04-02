<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PresencePerMonthExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        $year = now()->year;

        for ($month = 1; $month <= 12; $month++) {
            $sheets[] = new PresenceExport($year, $month);
        }

        return $sheets;
    }
}