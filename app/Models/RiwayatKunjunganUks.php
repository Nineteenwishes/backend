<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKunjunganUks extends Model
{
    use HasFactory;

    protected $table = 'riwayat_kunjungan_uks';

    protected $fillable = [
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
        'tanggal'
    ];

    // Relasi dengan model Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'nis', 'nis');
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeFilterByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    // Scope untuk filter berdasarkan status
    public function scopeFilterByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}