<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterSubBidang;
use Illuminate\Http\Request;
use App\Http\Requests\MasterData\StoreMasterSubBidangRequest;
use App\Http\Requests\MasterData\UpdateMasterSubBidangRequest;

use App\Services\MasterData\MasterSubBidangService;

class MasterSubBidangController extends Controller
{
    protected $subBidangService;

    public function __construct(MasterSubBidangService $subBidangService)
    {
        $this->subBidangService = $subBidangService;
    }

    /**
     * Store a new sub bidang (AJAX).
     */
    public function store(StoreMasterSubBidangRequest $request)
    {
        $subBidang = $this->subBidangService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sub Bidang berhasil ditambahkan!',
            'data'    => $subBidang,
        ]);
    }

    /**
     * Update sub bidang (AJAX).
     */
    public function update(UpdateMasterSubBidangRequest $request, MasterSubBidang $masterSubBidang)
    {
        $this->subBidangService->update($masterSubBidang, $request->validated());

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
        $this->subBidangService->delete($masterSubBidang);

        return response()->json([
            'success' => true,
            'message' => 'Sub Bidang berhasil dihapus!',
        ]);
    }
}
