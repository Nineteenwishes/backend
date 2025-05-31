<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::all();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|unique:students',
            'nama' => 'required|string',
            'kelas' => 'required|string'
        ]);

        $student = Student::create($request->only(['nis', 'nama', 'kelas']));
        return response()->json([
            'message' => 'Data siswa berhasil ditambahkan',
            'data' => $student
        ], 201);
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'nis' => ['required', 'string', Rule::unique('students')->ignore($student->id)],
            'nama' => 'required|string',
            'kelas' => 'required|string'
        ]);

        $student->update($request->only(['nis', 'nama', 'kelas']));

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $student
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json([
            'message' => 'Data siswa berhasil dihapus'
        ]);
    }

    /**
     * Endpoint untuk mencari siswa berdasarkan NIS
     */
    public function findByNis($nis)
    {
        $student = Student::where('nis', $nis)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Siswa dengan NIS tersebut tidak ditemukan'
            ], 404);
        }

        return response()->json($student);
    }
}
