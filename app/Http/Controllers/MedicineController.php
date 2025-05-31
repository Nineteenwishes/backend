<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicineController extends Controller
{
    // GET /medicines
    public function index()
    {
        $medicines = Medicine::all();
        return response()->json($medicines);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'nullable|string|max:255',
            'stok' => 'required|integer',
            'dosis' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('medicines', 'public');
            $validated['foto'] = $path;
        }

        $medicine = Medicine::create($validated);

        return response()->json([
            'message' => 'Medicine created successfully',
            'data' => $medicine
        ], 201);
    }

    public function show($id)
    {
        $medicine = Medicine::findOrFail($id);
        return response()->json($medicine);
    }
    
    // PUT /medicines/{id}
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'nullable|string|max:255',
            'stok' => 'required|integer',
            'dosis' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($medicine->foto) {
                Storage::disk('public')->delete($medicine->foto);
            }
            
            // Store new photo
            $file = $request->file('foto');
            $path = $file->store('medicines', 'public');
            $validated['foto'] = $path;
        }

        $medicine->update($validated);

        return response()->json([
            'message' => 'Medicine updated successfully',
            'data' => $medicine
        ]);
    }

    // DELETE /medicines/{id}
    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);

        if ($medicine->foto) {
            Storage::disk('public')->delete($medicine->foto);
        }

        $medicine->delete();

        return response()->json([
            'message' => 'Medicine deleted successfully'
        ]);
    }

 
   

}
