<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'foto',
        'nama',
        'jenis',
        'stok',
        'dosis',
        'deskripsi'
    ];

    // Tambahkan relasi dengan UksVisit
    public function uksVisits()
    {
        return $this->hasMany(UksVisit::class);
    }
}