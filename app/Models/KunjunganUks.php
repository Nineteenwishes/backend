<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KunjunganUks extends Model
{
    use HasFactory;

    protected $table = 'kunjungan_uks';
    
    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'gejala',
        'keterangan',
        'obat',
        'foto',
        'jam_masuk',
        'jam_keluar',
        'status'
    ];

    /**
     * Relasi ke model Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'nis', 'nis');
    }
}