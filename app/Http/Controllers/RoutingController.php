<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoutingController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        // $this->
        // middleware('auth')->
        // except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $role = strtoupper(trim($user->akses?->nama_akses ?? ''));
            if ($role === 'AUDITEE') {
                return redirect()->route('audit.pemantauan.index');
            }
            return redirect()->route('audit.dashboard');
        } else {
            return redirect('login');
        }
    }

    public function perencanaanAuditForm()
    {
        $auditees = \App\Models\MasterData\MasterAuditee::all();
        $auditors = \App\Models\MasterData\MasterUser::with('akses')->whereHas('akses', function($q) {
            $q->where('nama_akses', 'Auditor');
        })->get();
        return view('audit.perencanaan.create', compact('auditees', 'auditors'));
    }

    public function masterKodeAoi()
    {
        try {
            $data = \App\Models\MasterData\MasterKodeAoi::all();
            return view('master-data.kode-aoi.index', compact('data'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function masterKodeRisk()
    {
        $data = \App\Models\MasterData\MasterKodeRisk::all();
        return view('master-data.kode-risk.index', compact('data'));
    }

    public function masterAuditee()
    {
        $data = \App\Models\MasterData\MasterAuditee::all();
        return view('master-data.auditee.index', compact('data'));
    }

    public function masterUser()
    {
        // Hide users with Superadmin access from the view
        $data = \App\Models\MasterData\MasterUser::with(['akses', 'auditee'])
            ->whereHas('akses', function($query) {
                $query->where('nama_akses', '!=', 'Superadmin');
            })
            ->get();
        return view('master-data.user.index', compact('data'));
    }

    public function masterAksesUser()
    {
        $data = \App\Models\MasterData\MasterAksesUser::all();
        return view('master-data.akses-user.index', compact('data'));
    }

    public function tabelPerencanaanAudit()
    {
        $data = \App\Models\Audit\PerencanaanAudit::with('auditee')->get();
        return view('audit.perencanaan.index', compact('data'));
    }
}
