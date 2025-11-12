<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterAksesUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterUserController extends Controller
{
    public function index()
    {
        $data = MasterUser::with(['akses', 'auditee'])->get();
        return view('master-data.user.index', compact('data'));
    }

    public function create()
    {
        $auditees = MasterAuditee::all();
        $aksesUsers = MasterAksesUser::all();
        return view('master-data.user.create', compact('auditees', 'aksesUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:master_user,username',
            'nip' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'master_auditee_id' => 'required|exists:master_auditee,id',
            'master_akses_user_id' => 'required|exists:master_akses_user,id',
        ]);

        MasterUser::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'nip' => $request->nip,
            'password' => Hash::make($request->password),
            'master_auditee_id' => $request->master_auditee_id,
            'master_akses_user_id' => $request->master_akses_user_id,
        ]);

        return redirect()->route('master.user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(MasterUser $masterUser)
    {
        $auditees = MasterAuditee::all();
        $aksesUsers = MasterAksesUser::all();
        return view('master-data.user.edit', compact('masterUser', 'auditees', 'aksesUsers'));
    }

    public function update(Request $request, MasterUser $masterUser)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:master_user,username,' . $masterUser->id,
            'nip' => 'required|string|max:255',
            'master_auditee_id' => 'required|exists:master_auditee,id',
            'master_akses_user_id' => 'required|exists:master_akses_user,id',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'nip' => $request->nip,
            'master_auditee_id' => $request->master_auditee_id,
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