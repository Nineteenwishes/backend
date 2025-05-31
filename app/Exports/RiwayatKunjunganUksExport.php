<?php

namespace App\Exports;

use App\Models\RiwayatKunjunganUks;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class RiwayatKunjunganUksExport implements FromCollection, WithHeadings, WithMultipleSheets, WithTitle
{
    protected $year;
    protected $month;
    protected $startDate;
    protected $endDate;

    public function __construct($year = null, $month = null, $startDate = null, $endDate = null)
    {
        $this->year = $year;
        $this->month = $month;
        $this->startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $this->endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : null;
    }

    public function collection()
    {
        $query = RiwayatKunjunganUks::query();

        if ($this->startDate && $this->endDate) {
            // Filter by date range (for weekly or specific period)
            $query->whereBetween('tanggal', [$this->startDate, $this->endDate]);
        } else if ($this->year) {
            // Filter by year (default or if only year is provided)
            $query->whereYear('tanggal', $this->year);
            if ($this->month) {
                // Filter by month if provided along with year
                $query->whereMonth('tanggal', $this->month);
            }
        } else {
            // Default to current year if no parameters are provided
            $query->whereYear('tanggal', Carbon::now()->year);
        }

        return $query->get([
            'nis',
            'nama',
            'kelas',
            'gejala',
            'keterangan',
            'obat',
            'foto',
            'status',
            'jam_masuk',
            'jam_keluar',
            'tanggal',
        ]);
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama',
            'Kelas',
            'Gejala',
            'Keterangan',
            'Obat',
            'Foto',
            'Status',
            'Jam Masuk',
            'Jam Keluar',
            'Tanggal',
        ];
    }

    public function sheets(): array
    {
        $sheets = [];

        // If only year is provided, create a sheet for each month
        if ($this->year && !$this->month && !$this->startDate && !$this->endDate) {
            for ($month = 1; $month <= 12; $month++) {
                $sheets[] = new RiwayatKunjunganUksExport($this->year, $month);
            }
        } else {
            // Otherwise, return a single sheet with the current filter (year/month or date range)
            $sheets[] = $this;
        }

        return $sheets;
    }

    public function title(): string
    {
        if ($this->month) {
            return Carbon::create(null, $this->month, 1)->monthName;
        }
        // Fallback title if no month is set (e.g., for weekly export or single year export)
        return 'Data Kunjungan UKS';
    }
}
