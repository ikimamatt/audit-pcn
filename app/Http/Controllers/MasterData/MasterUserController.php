<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterAksesUser;
use App\Models\MasterData\MasterArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\MasterData\StoreMasterUserRequest;
use App\Http\Requests\MasterData\UpdateMasterUserRequest;
use App\Http\Requests\MasterData\ResetPasswordMasterUserRequest;

use App\Services\MasterData\MasterUserService;

class MasterUserController extends Controller
{
    protected $userService;

    public function __construct(MasterUserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        // Hide users with Superadmin access from the view
        $data = MasterUser::with(['akses', 'auditee', 'area'])
            ->whereHas('akses', function($query) {
                $query->where('nama_akses', '!=', 'Superadmin');
            })
            ->get();
        return view('master-data.user.index', compact('data'));
    }

    public function create()
    {
        $auditees = MasterAuditee::all();
        $areas    = MasterArea::with('region')->orderBy('kd_area')->get();
        $allowedAkses = ['AUDITEE', 'ASMAN SPI', 'KSPI', 'AUDITOR', 'SUPER ADMIN', 'VIEW BOD'];
        $aksesUsers = MasterAksesUser::whereIn('nama_akses', $allowedAkses)
            ->where('nama_akses', '!=', 'Superadmin')
            ->get()
            ->sortBy(function($item) use ($allowedAkses) {
                return array_search($item->nama_akses, $allowedAkses);
            })
            ->values();
        return view('master-data.user.create', compact('auditees', 'aksesUsers', 'areas'));
    }

    public function store(StoreMasterUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()->route('master.user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(MasterUser $masterUser)
    {
        // Prevent editing Superadmin users
        if ($masterUser->akses && $masterUser->akses->nama_akses === 'Superadmin') {
            return redirect()->route('master.user.index')
                ->with('error', 'Superadmin user cannot be edited through this interface.');
        }
        
        $auditees = MasterAuditee::all();
        $areas    = MasterArea::with('region')->orderBy('kd_area')->get();
        $allowedAkses = ['AUDITEE', 'ASMAN SPI', 'KSPI', 'AUDITOR', 'SUPER ADMIN', 'VIEW BOD'];
        
        if ($masterUser->master_akses_user_id) {
            $currentAksesUser = MasterAksesUser::find($masterUser->master_akses_user_id);
            if ($currentAksesUser && !in_array($currentAksesUser->nama_akses, $allowedAkses)) {
                $allowedAkses[] = $currentAksesUser->nama_akses;
            }
        }
        
        $aksesUsers = MasterAksesUser::whereIn('nama_akses', $allowedAkses)
            ->where('nama_akses', '!=', 'Superadmin')
            ->get()
            ->sortBy(function($item) use ($allowedAkses) {
                return array_search($item->nama_akses, $allowedAkses);
            })
            ->values();
            
        return view('master-data.user.edit', compact('masterUser', 'auditees', 'aksesUsers', 'areas'));
    }

    public function update(UpdateMasterUserRequest $request, MasterUser $masterUser)
    {
        $this->userService->update($masterUser, $request->validated());

        return redirect()->route('master.user.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(MasterUser $masterUser)
    {
        // Prevent deleting Superadmin users
        if ($masterUser->akses && $masterUser->akses->nama_akses === 'Superadmin') {
            return redirect()->route('master.user.index')
                ->with('error', 'Superadmin user cannot be deleted through this interface.');
        }
        
        try {
            $this->userService->delete($masterUser);
            return redirect()->route('master.user.index')->with('success', 'User berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.user.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }

    public function resetPassword(ResetPasswordMasterUserRequest $request, MasterUser $masterUser)
    {
        $this->userService->resetPassword($masterUser, $request->password);

        return redirect()->route('master.user.index')->with('success', 'Password user ' . $masterUser->nama . ' berhasil direset!');
    }
} 