<?php

namespace App\Services\Audit;

use App\Models\EntryMeeting;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EntryMeetingService
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create a new Entry Meeting.
     *
     * @param array $data
     * @return EntryMeeting
     */
    public function create(array $data): EntryMeeting
    {
        return DB::transaction(function () use ($data) {
            $undanganPath = $this->fileUploadService->store($data['file_undangan_file'], 'entry_meeting');
            $absensiPath = $this->fileUploadService->store($data['file_absensi_file'], 'entry_meeting');

            return EntryMeeting::create([
                'program_kerja_audit_id' => $data['program_kerja_audit_id'],
                'tanggal'                => $data['planned_meeting_date'],
                'actual_meeting_date'    => $data['actual_meeting_date'] ?? null,
                'auditee_id'             => $data['auditee_id'],
                'file_undangan'          => $undanganPath,
                'file_absensi'           => $absensiPath,
            ]);
        });
    }

    /**
     * Update an existing Entry Meeting.
     *
     * @param EntryMeeting $item
     * @param array $data
     * @return EntryMeeting
     */
    public function update(EntryMeeting $item, array $data): EntryMeeting
    {
        return DB::transaction(function () use ($item, $data) {
            $updateData = [
                'program_kerja_audit_id' => $data['program_kerja_audit_id'],
                'tanggal'                => $data['planned_meeting_date'],
                'actual_meeting_date'    => $data['actual_meeting_date'] ?? null,
                'auditee_id'             => $data['auditee_id'],
            ];

            if (isset($data['file_undangan_file'])) {
                $updateData['file_undangan'] = $this->fileUploadService->replace(
                    $item->file_undangan,
                    $data['file_undangan_file'],
                    'entry_meeting'
                );
            }

            if (isset($data['file_absensi_file'])) {
                $updateData['file_absensi'] = $this->fileUploadService->replace(
                    $item->file_absensi,
                    $data['file_absensi_file'],
                    'entry_meeting'
                );
            }

            $item->update($updateData);
            return $item;
        });
    }

    /**
     * Delete an Entry Meeting and its files.
     *
     * @param EntryMeeting $item
     * @return void
     */
    public function delete(EntryMeeting $item): void
    {
        DB::transaction(function () use ($item) {
            $this->fileUploadService->delete($item->file_undangan);
            $this->fileUploadService->delete($item->file_absensi);
            $item->delete();
        });
    }
}
