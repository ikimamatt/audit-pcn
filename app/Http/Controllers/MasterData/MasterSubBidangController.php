<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterSubBidang;
use Illuminate\Http\Request;

class MasterSubBidangController extends Controller
{
    /**
     * Store a new sub bidang (AJAX).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'master_bidang_id' => 'required|exists:master_auditee,id',
        ]);

        $subBidang = MasterSubBidang::create($request->only(['nama', 'master_bidang_id']));

        return response()->json([
            'success' => true,
            'message' => 'Sub Bidang berhasil ditambahkan!',
            'data'    => $subBidang,
        ]);
    }

    /**
     * Update sub bidang (AJAX).
     */
    public function update(Request $request, MasterSubBidang $masterSubBidang)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $masterSubBidang->update($request->only(['nama']));

        return response()->json([
            'success' => true,
            'message' => 'Sub Bidang berhasil diperbarui!',
            'data'    => $masterSubBidang,
        ]);
    }

    /**
     * Delete sub bidang (AJAX).
     */
    public function destroy(MasterSubBidang $masterSubBidang)
    {
        $masterSubBidang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub Bidang berhasil dihapus!',
        ]);
    }
}
