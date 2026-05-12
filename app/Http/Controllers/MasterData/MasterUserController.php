<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterAksesUser;
use App\Models\MasterData\MasterUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterUserController extends Controller
{
    public function index()
    {
        // Hide users with Superadmin access from the view
        $data = MasterUser::with(['akses', 'auditee', 'unit'])
            ->whereHas('akses', function($query) {
                $query->where('nama_akses', '!=', 'Superadmin');
            })
            ->get();
        return view('master-data.user.index', compact('data'));
    }

    public function create()
    {
        $auditees = MasterAuditee::all();
        $units    = MasterUnit::orderBy('kode_unit')->get();
        $allowedAkses = ['AUDITEE', 'ASMAN SPI', 'KSPI', 'AUDITOR', 'SUPER ADMIN', 'VIEW BOD'];
        $aksesUsers = MasterAksesUser::whereIn('nama_akses', $allowedAkses)
            ->where('nama_akses', '!=', 'Superadmin')
            ->get()
            ->sortBy(function($item) use ($allowedAkses) {
                return array_search($item->nama_akses, $allowedAkses);
            })
            ->values();
        return view('master-data.user.create', compact('auditees', 'aksesUsers', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'                 => 'required|string|max:255',
            'username'             => 'required|string|max:255|unique:master_user,username',
            'nip'                  => 'required|string|max:255',
            'password'             => 'required|string|min:6',
            'email'                => 'nullable|email|max:255',
            'no_telpon'            => 'nullable|string|max:20',
            'jabatan'              => 'nullable|string|max:255',
            'master_auditee_id'    => 'required|exists:master_auditee,id',
            'master_unit_id'       => 'required|exists:master_unit,id',
            'master_akses_user_id' => 'required|exists:master_akses_user,id',
        ]);

        MasterUser::create([
            'nama'                 => $request->nama,
            'username'             => $request->username,
            'nip'                  => $request->nip,
            'password'             => Hash::make($request->password),
            'email'                => $request->email,
            'no_telpon'            => $request->no_telpon,
            'jabatan'              => $request->jabatan,
            'master_auditee_id'    => $request->master_auditee_id,
            'master_unit_id'       => $request->master_unit_id,
            'master_akses_user_id' => $request->master_akses_user_id,
        ]);

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
        $units    = MasterUnit::orderBy('kode_unit')->get();
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
            
        return view('master-data.user.edit', compact('masterUser', 'auditees', 'aksesUsers', 'units'));
    }

    public function update(Request $request, MasterUser $masterUser)
    {
        $request->validate([
            'nama'                 => 'required|string|max:255',
            'username'             => 'required|string|max:255|unique:master_user,username,' . $masterUser->id,
            'nip'                  => 'required|string|max:255',
            'email'                => 'nullable|email|max:255',
            'no_telpon'            => 'nullable|string|max:20',
            'jabatan'              => 'nullable|string|max:255',
            'master_auditee_id'    => 'required|exists:master_auditee,id',
            'master_unit_id'       => 'required|exists:master_unit,id',
            'master_akses_user_id' => 'required|exists:master_akses_user,id',
        ]);

        $data = [
            'nama'                 => $request->nama,
            'username'             => $request->username,
            'nip'                  => $request->nip,
            'email'                => $request->email,
            'no_telpon'            => $request->no_telpon,
            'jabatan'              => $request->jabatan,
            'master_auditee_id'    => $request->master_auditee_id,
            'master_unit_id'       => $request->master_unit_id,
            'master_akses_user_id' => $request->master_akses_user_id,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:6',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $masterUser->update($data);

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
            $masterUser->delete();
            return redirect()->route('master.user.index')->with('success', 'User berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('master.user.index')->with('error', 'Data tidak bisa dihapus karena masih digunakan di data lain.');
            }
            throw $e;
        }
    }
} 