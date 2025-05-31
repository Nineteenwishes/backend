<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama',
        'kelas'
    ];

    /**
     * Cek apakah siswa sedang berada di UKS
     */
    public function getIsInUksAttribute()
    {
        return $this->sickRecords()
            ->where('status', true)
            ->exists();
    }

    public function uksVisits()
{
    return $this->hasMany(KunjunganUks::class, 'nis', 'nis');
}

}