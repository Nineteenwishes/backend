<?php

namespace App\Http\Controllers;

use App\Models\KunjunganUks;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\RiwayatKunjunganUks;

class KunjunganUksController extends Controller
{
    /**
     * Menampilkan semua data kunjungan UKS
     */
    public function index()
    {
        $today = now()->toDateString();

        $kunjungan = KunjunganUks::whereDate('created_at', $today)->get();

        return response()->json($kunjungan);
    }


    /**
     * Menambah data kunjungan UKS baru
     */
    public function store(Request $request)
    {
        try {
            // Ambil data siswa berdasarkan NIS
            $student = Student::where('nis', $request->nis)->firstOrFail();

            // Siapkan data kunjungan
            $data = [
                'nis' => $student->nis,
                'nama' => $student->nama, // Tambahkan nama
                'kelas' => $student->kelas, // Tambahkan kelas
                'gejala' => $request->gejala,
                'keterangan' => $request->keterangan,
                'obat' => $request->obat,
                'status' => 'masuk uks',
                'jam_masuk' => Carbon::now()->format('H:i')
            ];

            // Handle upload foto jika ada
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoPath = $foto->store('kunjungan-uks', 'public');
                $data['foto'] = $fotoPath;
            }

            // Buat record kunjungan
            $kunjungan = KunjunganUks::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Siswa berhasil ditambahkan ke UKS',
                'data' => $kunjungan
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan siswa ke UKS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kunjungan = KunjunganUks::findOrFail($id);

            // Cek apakah status sudah keluar, misalnya status = 'keluar' atau bisa boolean
            if ($kunjungan->status === 'keluar uks') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data kunjungan sudah keluar UKS, tidak dapat diubah'
                ], 403); // Forbidden
            }

            // Validasi input
            $request->validate([
                'nis' => 'required|string|exists:students,nis',
                'gejala' => 'required|string',
                'keterangan' => 'nullable|string',
                'obat' => 'nullable|string',
                'foto' => 'nullable|image|max:2048'
            ]);

            // Dapatkan data siswa berdasarkan NIS baru
            $student = Student::where('nis', $request->nis)->firstOrFail();

            // Siapkan data untuk update
            $data = [
                'nis' => $request->nis,
                'nama' => $student->nama,
                'kelas' => $student->kelas,
                'gejala' => $request->gejala,
                'keterangan' => $request->keterangan,
                'obat' => $request->obat
            ];

            // Handle upload foto jika ada
            if ($request->hasFile('foto')) {
                if ($kunjungan->foto) {
                    Storage::disk('public')->delete($kunjungan->foto);
                }
                $foto = $request->file('foto');
                $fotoPath = $foto->store('kunjungan-uks', 'public');
                $data['foto'] = $fotoPath;
            }

            // Update data kunjungan
            $kunjungan->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data kunjungan UKS berhasil diperbarui',
                'data' => $kunjungan
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data siswa atau kunjungan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Menampilkan detail kunjungan UKS berdasarkan ID
     */
    public function show($id)
    {
        try {
            // Ambil data kunjungan dengan relasi student
            $kunjungan = KunjunganUks::with('student')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $kunjungan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data kunjungan tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update status kunjungan ketika siswa keluar UKS
     */
    public function keluar($id)
    {
        $kunjungan = KunjunganUks::findOrFail($id);

        $kunjungan->update([
            'status' => 'Keluar UKS',
            'jam_keluar' => now()->format('H:i'),
        ]);

        // Simpan ke riwayat
        RiwayatKunjunganUks::create([
            'nis' => $kunjungan->nis,
            'nama' => $kunjungan->nama,
            'kelas' => $kunjungan->kelas,
            'gejala' => $kunjungan->gejala,
            'jam_masuk' => $kunjungan->jam_masuk,
            'jam_keluar' => now()->format('H:i'),
            'keterangan' => $kunjungan->keterangan,
            'status' => 'Keluar UKS',
            'tanggal' => now()->toDateString(),
            'obat' => $kunjungan->obat,
            'foto' => $kunjungan->foto,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Siswa berhasil ditandai keluar dan dipindahkan ke riwayat.',
        ]);
    }
}
