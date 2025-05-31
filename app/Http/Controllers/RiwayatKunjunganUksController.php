<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKunjunganUks;
use App\Models\KunjunganUks;
use App\Exports\RiwayatKunjunganUksExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RiwayatKunjunganUksController extends Controller
{
    /**
     * Get all visit history records
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $riwayat = RiwayatKunjunganUks::latest()->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $riwayat
        ]);
    }

    /**
     * Store a new visit history record by moving from active visits
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'kunjungan_id' => 'required|exists:kunjungan_uks,id'
        ]);

        $kunjungan = KunjunganUks::findOrFail($request->kunjungan_id);

        if ($kunjungan->status === 'masuk uks') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat memindahkan data. Siswa masih dalam status masuk UKS.'
            ], 400);
        }

        $riwayat = $this->createHistoryFromVisit($kunjungan);
        $kunjungan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dipindahkan ke riwayat',
            'data' => $riwayat
        ]);
    }

    /**
     * Create history record from visit data
     *
     * @param KunjunganUks $kunjungan
     * @return RiwayatKunjunganUks
     */
    protected function createHistoryFromVisit(KunjunganUks $kunjungan): RiwayatKunjunganUks
    {
        return RiwayatKunjunganUks::create([
            'nis' => $kunjungan->nis,
            'nama' => $kunjungan->nama,
            'kelas' => $kunjungan->kelas,
            'gejala' => $kunjungan->gejala,
            'keterangan' => $kunjungan->keterangan,
            'obat' => $kunjungan->obat,
            'foto' => $kunjungan->foto,
            'status' => 'keluar uks',
            'jam_masuk' => $kunjungan->jam_masuk,
            'jam_keluar' => $kunjungan->jam_keluar,
            'tanggal' => Carbon::now()->toDateString()
        ]);
    }

    /**
     * Get a single visit history record
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $riwayat = RiwayatKunjunganUks::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $riwayat
        ]);
    }

    /**
     * Update a visit history record
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $riwayat = RiwayatKunjunganUks::findOrFail($id);
        $riwayat->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data riwayat berhasil diperbarui',
            'data' => $riwayat
        ]);
    }

    /**
     * Delete a visit history record
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $riwayat = RiwayatKunjunganUks::findOrFail($id);
        $riwayat->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data riwayat berhasil dihapus'
        ]);
    }

    /**
     * Export visit history to Excel
     *
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function export(Request $request): BinaryFileResponse
    {
        $exportParameters = $this->prepareExportParameters($request);
        $export = new RiwayatKunjunganUksExport(
            $exportParameters['year'],
            $exportParameters['month'],
            $exportParameters['startDate'],
            $exportParameters['endDate']
        );

        return Excel::download($export, $exportParameters['fileName']);
    }

    /**
     * Prepare export parameters based on request
     *
     * @param Request $request
     * @return array
     */
    protected function prepareExportParameters(Request $request): array
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $fileName = 'riwayat_kunjungan_uks_pekan_' .
                       Carbon::parse($startDate)->format('Ymd') . '-' .
                       Carbon::parse($endDate)->format('Ymd') . '.xlsx';
        } else if ($year && $month) {
            $fileName = 'riwayat_kunjungan_uks_' .
                       $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx';
        } else if ($year) {
            $fileName = 'riwayat_kunjungan_uks_tahun_' . $year . '.xlsx';
        } else {
            $fileName = 'riwayat_kunjungan_uks_semua_data.xlsx';
        }

        return [
            'year' => $year,
            'month' => $month,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'fileName' => $fileName
        ];
    }
}