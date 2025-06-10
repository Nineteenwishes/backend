<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'hari',
    ];
}
