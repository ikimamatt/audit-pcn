<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\ExitMeetingUpload;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExitMeetingUploadController extends Controller
{
    public function index()
    {
        $data = ExitMeetingUpload::with(['auditee', 'approvedBy'])->get();
        return view('audit.upload.exit-meeting.index', compact('data'));
    }

    public function create()
    {
        $auditees = MasterAuditee::all();
        return view('audit.upload.exit-meeting.create', compact('auditees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_exit_meeting' => 'required|date',
            'auditee_id' => 'required|exists:master_auditee,id',
            'file_undangan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_absensi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $undanganPath = $request->file('file_undangan')->store('exit_meeting/undangan', 'public');
        $absensiPath = $request->file('file_absensi')->store('exit_meeting/absensi', 'public');
        ExitMeetingUpload::create([
            'tanggal_exit_meeting' => $request->tanggal_exit_meeting,
            'auditee_id' => $request->auditee_id,
            'file_undangan' => $undanganPath,
            'file_absensi' => $absensiPath,
        ]);
        return redirect()->route('audit.unggah-dokumen.index')->with('success', 'Data Exit Meeting berhasil diupload!');
    }

    public function edit($id)
    {
        $exitMeeting = ExitMeetingUpload::findOrFail($id);
        $auditees = MasterAuditee::all();
        return view('audit.upload.exit-meeting.edit', compact('exitMeeting', 'auditees'));
    }

    public function update(Request $request, $id)
    {
        $item = ExitMeetingUpload::findOrFail($id);
        $request->validate([
            'tanggal_exit_meeting' => 'required|date',
            'auditee_id' => 'required|exists:master_auditee,id',
            'file_undangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_absensi' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $data = [
            'tanggal_exit_meeting' => $request->tanggal_exit_meeting,
            'auditee_id' => $request->auditee_id,
        ];
        if ($request->hasFile('file_undangan')) {
            if ($item->file_undangan) Storage::disk('public')->delete($item->file_undangan);
            $data['file_undangan'] = $request->file('file_undangan')->store('exit_meeting/undangan', 'public');
        }
        if ($request->hasFile('file_absensi')) {
            if ($item->file_absensi) Storage::disk('public')->delete($item->file_absensi);
            $data['file_absensi'] = $request->file('file_absensi')->store('exit_meeting/absensi', 'public');
        }
        $item->update($data);
        return redirect()->route('audit.unggah-dokumen.index')->with('success', 'Data Exit Meeting berhasil diupdate!');
    }

    public function destroy($id)
    {
        $item = ExitMeetingUpload::findOrFail($id);
        if ($item->file_undangan) Storage::disk('public')->delete($item->file_undangan);
        if ($item->file_absensi) Storage::disk('public')->delete($item->file_absensi);
        $item->delete();
        return redirect()->route('audit.unggah-dokumen.index')->with('success', 'Data Exit Meeting berhasil dihapus!');
    }

    public function show($id)
    {
        $item = ExitMeetingUpload::with(['auditee', 'approvedBy'])->findOrFail($id);
        return view('audit.upload.exit-meeting.show', compact('item'));
    }

    public function approval(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);
        $item = ExitMeetingUpload::findOrFail($id);
        if ($request->action === 'approve') {
            $item->update([
                'status_approval' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            $message = 'Exit Meeting berhasil diapprove!';
        } else {
            $item->update([
                'status_approval' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            $message = 'Exit Meeting berhasil direject!';
        }
        return redirect()->route('audit.unggah-dokumen.index')->with('success', $message);
    }
} 