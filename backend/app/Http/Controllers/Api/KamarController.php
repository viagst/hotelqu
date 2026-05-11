<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::with('tipeKamar');

        if ($request->has('id_tipe_kamar')) {
            $query->where('id_tipe_kamar', $request->id_tipe_kamar);
        }

        if ($request->has('status_kamar')) {
            $query->where('status_kamar', $request->status_kamar);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tipe_kamar' => 'required|exists:tipe_kamars,id',
            'nomor_kamar' => 'required|string|max:10|unique:kamars,nomor_kamar',
            'status_kamar' => 'nullable|in:tersedia,kotor,perbaikan,bersih,kosong',
        ]);

        $kamar = Kamar::create($request->only('id_tipe_kamar', 'nomor_kamar', 'status_kamar'));

        return response()->json([
            'message' => 'Kamar berhasil ditambahkan',
            'data' => $kamar->load('tipeKamar'),
        ], 201);
    }

    public function show($id)
    {
        $kamar = Kamar::with('tipeKamar')->findOrFail($id);
        return response()->json($kamar);
    }

    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $request->validate([
            'id_tipe_kamar' => 'sometimes|exists:tipe_kamars,id',
            'nomor_kamar' => 'sometimes|string|max:10|unique:kamars,nomor_kamar,' . $id,
            'status_kamar' => 'nullable|in:tersedia,kotor,perbaikan,bersih,kosong',
        ]);

        $kamar->update($request->only('id_tipe_kamar', 'nomor_kamar', 'status_kamar'));

        return response()->json([
            'message' => 'Kamar berhasil diupdate',
            'data' => $kamar->fresh()->load('tipeKamar'),
        ]);
    }

    public function destroy($id)
    {
        $kamar = Kamar::findOrFail($id);
        $kamar->delete();

        return response()->json(['message' => 'Kamar berhasil dihapus']);
    }
}
