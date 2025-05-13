<?php

namespace App\Exports;

use App\Models\Presence;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class PresenceExport implements FromView, WithStyles, WithDrawings, WithTitle
{
    protected $presences;
    protected $filter;
    protected $tab;
    protected $dateFrom;
    protected $dateTo;
    protected $title;
    protected $sheetTitle;

    /**
     * Constructor untuk menyimpan data yang akan diexport
     * 
     * @param array $presences Data presensi yang akan diexport
     * @param string $filter Filter waktu yang dipilih (Hari Ini, Kemarin, dll)
     * @param string $tab Tab yang dipilih (all, hadir, izin, sakit, alpa)
     * @param string|null $dateFrom Tanggal awal jika filter Custom
     * @param string|null $dateTo Tanggal akhir jika filter Custom
     */
    public function __construct($presences, $filter, $tab, $dateFrom = null, $dateTo = null)
    {
        $this->presences = $presences;
        $this->filter = $filter;
        $this->tab = $tab;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        
        // Membuat judul file berdasarkan filter dan tab yang dipilih
        $this->setTitle();
    }

    /**
     * Membuat judul file berdasarkan filter dan tab yang dipilih
     */
    protected function setTitle()
    {
        // Menentukan status berdasarkan tab
        $status = 'Semua Siswa';
        if ($this->tab == 'hadir') {
            $status = 'Siswa Hadir';
        } elseif ($this->tab == 'izin') {
            $status = 'Siswa Izin';
        } elseif ($this->tab == 'sakit') {
            $status = 'Siswa Sakit';
        } elseif ($this->tab == 'alpa') {
            $status = 'Siswa Alpa';
        } elseif ($this->tab == 'terlambat') {
            $status = 'Siswa Terlambat';
        }

        // Menentukan periode berdasarkan filter
        $periode = $this->filter;
        if ($this->filter == 'Custom' && $this->dateFrom && $this->dateTo) {
            $from = Carbon::parse($this->dateFrom)->format('d/m/Y');
            $to = Carbon::parse($this->dateTo)->format('d/m/Y');
            $periode = "Tanggal $from s/d $to";
        }

        $this->title = "Presensi $status $periode";
        $this->sheetTitle = $this->tab == 'all' ? 'Semua Data' : ucfirst($this->tab);
    }

    /**
     * Mengembalikan view yang akan digunakan untuk export
     */
    public function view(): View
    {
        return view('exports.presence', [
            'presences' => $this->presences,
            'filter' => $this->filter,
            'tab' => $this->tab,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'title' => $this->title
        ]);
    }

    /**
     * Mengatur style untuk worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Mengatur style untuk header
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFont()->setSize(14);
        $sheet->getStyle('A2:K2')->getFont()->setSize(12);
        $sheet->getStyle('A3:K3')->getFont()->setSize(12);
        $sheet->getStyle('A4:K4')->getFont()->setSize(12);
        
        // Mengatur style untuk judul tabel
        $sheet->getStyle('A6:K6')->getFont()->setBold(true);
        $sheet->getStyle('A6:K6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6:K6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A6:K6')->getFill()->getStartColor()->setRGB('CCCCCC');
        
        // Mengatur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);  // No
        $sheet->getColumnDimension('B')->setWidth(15); // NIS
        $sheet->getColumnDimension('C')->setWidth(30); // Nama
        $sheet->getColumnDimension('D')->setWidth(15); // Kelas
        $sheet->getColumnDimension('E')->setWidth(20); // Waktu Masuk
        $sheet->getColumnDimension('F')->setWidth(15); // Status Masuk
        $sheet->getColumnDimension('G')->setWidth(20); // Waktu Keluar
        $sheet->getColumnDimension('H')->setWidth(15); // Status Keluar
        $sheet->getColumnDimension('I')->setWidth(30); // Alasan Masuk Telat
        $sheet->getColumnDimension('J')->setWidth(30); // Alasan Pulang
        $sheet->getColumnDimension('K')->setWidth(30); // Keterangan Lainnya
        
        // Mengatur tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);
        
        // Mengatur border untuk tabel data
        $lastRow = count($this->presences) + 6;
        $sheet->getStyle('A6:K' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        return $sheet;
    }

    /**
     * Menambahkan gambar logo ke worksheet
     */
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('favicon.ico'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    /**
     * Mengatur judul sheet
     */
    public function title(): string
    {
        return $this->sheetTitle;
    }
}
