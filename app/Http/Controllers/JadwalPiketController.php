<?php

namespace App\Http\Controllers;

use App\Models\JadwalPiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JadwalPiketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwalPiket = JadwalPiket::all();
        return response()->json(['data' => $jadwalPiket]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:255',
            'hari' => 'required|string|max:50',  // Validasi kolom hari
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jadwalPiket = JadwalPiket::create($request->all());
        return response()->json(['message' => 'Jadwal piket berhasil ditambahkan', 'data' => $jadwalPiket], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jadwalPiket = JadwalPiket::find($id);

        if (!$jadwalPiket) {
            return response()->json(['message' => 'Jadwal piket tidak ditemukan'], 404);
        }

        return response()->json(['data' => $jadwalPiket]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jadwalPiket = JadwalPiket::find($id);

        if (!$jadwalPiket) {
            return response()->json(['message' => 'Jadwal piket tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nis' => 'sometimes|required|string|max:255',
            'nama' => 'sometimes|required|string|max:255',
            'kelas' => 'sometimes|required|string|max:255',
            'hari' => 'sometimes|required|string|max:50',  // Validasi kolom hari
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jadwalPiket->update($request->all());
        return response()->json(['message' => 'Jadwal piket berhasil diperbarui', 'data' => $jadwalPiket]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jadwalPiket = JadwalPiket::find($id);

        if (!$jadwalPiket) {
            return response()->json(['message' => 'Jadwal piket tidak ditemukan'], 404);
        }

        $jadwalPiket->delete();
        return response()->json(['message' => 'Jadwal piket berhasil dihapus']);
    }

    /**
     * Get jadwal piket berdasarkan hari tertentu.
     */
    public function getByHari(string $hari)
    {
        $jadwal = JadwalPiket::where('hari', $hari)->get();

        if ($jadwal->isEmpty()) {
            return response()->json(['message' => 'Jadwal piket untuk hari ' . $hari . ' tidak ditemukan'], 404);
        }

        return response()->json(['data' => $jadwal]);
    }
}
