<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalHelper
{
    /**
     * Process approval berjenjang
     * 
     * @param mixed $item Model item yang akan di-approve
     * @param string $action 'approve' or 'reject'
     * @param string|null $rejectionReason Alasan penolakan (required jika reject)
     * @return array ['success' => bool, 'message' => string]
     */
    public static function processApproval($item, $action, $rejectionReason = null)
    {
        if (!AuthHelper::canApproveReject()) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval/reject!'
            ];
        }

        $user = Auth::user();
        $isAsmanKspi = AuthHelper::isAsmanKspi();
        $isKspi = AuthHelper::isKspi();
        
        // Log untuk debugging
        Log::info('Processing approval', [
            'action' => $action,
            'table' => $item->getTable(),
            'id' => $item->getKey(),
            'current_status' => $item->status_approval,
            'isAsmanKspi' => $isAsmanKspi,
            'isKspi' => $isKspi,
            'user_id' => $user->id,
        ]);

        if ($action === 'approve') {
            // Level 1 Approval (ASMAN KSPI)
            if ($isAsmanKspi && $item->status_approval === 'pending') {
                try {
                    $tableName = $item->getTable();
                    $itemId = $item->getKey();
                    
                    $updateData = [
                        'status_approval' => 'approved_level1',
                        'approved_by_level1' => $user->id,
                        'approved_at_level1' => now(),
                    ];
                    
                    $updated = DB::table($tableName)
                        ->where('id', $itemId)
                        ->update($updateData);
                    
                    if ($updated === false || $updated === 0) {
                        Log::error('Failed to update approve level 1', [
                            'table' => $tableName,
                            'id' => $itemId,
                            'data' => $updateData
                        ]);
                        return [
                            'success' => false,
                            'message' => 'Gagal mengupdate status! Silakan coba lagi.'
                        ];
                    }
                    
                    $item->refresh();
                    
                    return [
                        'success' => true,
                        'message' => 'Data berhasil diapprove di Level 1 (ASMAN KSPI)!'
                    ];
                } catch (\Exception $e) {
                    Log::error('Error approving at level 1: ' . $e->getMessage());
                    return [
                        'success' => false,
                        'message' => 'Terjadi kesalahan saat approve data: ' . $e->getMessage()
                    ];
                }
            }

            // Level 2 Approval (KSPI)
            // Jika tidak ada user ASMAN KSPI, KSPI bisa langsung approve dari pending
            if ($isKspi && $item->status_approval === 'pending') {
                if (AuthHelper::hasAsmanKspiUser()) {
                    return [
                        'success' => false,
                        'message' => 'Data belum diapprove oleh ASMAN KSPI. Harap tunggu approval Level 1 terlebih dahulu!'
                    ];
                } else {
                    // Tidak ada ASMAN KSPI, KSPI bisa langsung approve final
                    try {
                        $tableName = $item->getTable();
                        $itemId = $item->getKey();
                        
                        $updateData = [
                            'status_approval' => 'approved',
                            'approved_by_level2' => $user->id,
                            'approved_at_level2' => now(),
                            'approved_by' => $user->id, // backward compatibility
                            'approved_at' => now(), // backward compatibility
                        ];
                        
                        $updated = DB::table($tableName)
                            ->where('id', $itemId)
                            ->update($updateData);
                        
                        if ($updated === false || $updated === 0) {
                            Log::error('Failed to update approve level 2 (no ASMAN KSPI)', [
                                'table' => $tableName,
                                'id' => $itemId,
                                'data' => $updateData
                            ]);
                            return [
                                'success' => false,
                                'message' => 'Gagal mengupdate status! Silakan coba lagi.'
                            ];
                        }
                        
                        $item->refresh();
                        
                        return [
                            'success' => true,
                            'message' => 'Data berhasil diapprove (KSPI) - Tidak ada ASMAN KSPI!'
                        ];
                    } catch (\Exception $e) {
                        Log::error('Error approving at level 2 (no ASMAN KSPI): ' . $e->getMessage());
                        return [
                            'success' => false,
                            'message' => 'Terjadi kesalahan saat approve data: ' . $e->getMessage()
                        ];
                    }
                }
            }
            
            if ($isKspi && $item->status_approval === 'approved_level1') {
                try {
                    $tableName = $item->getTable();
                    $itemId = $item->getKey();
                    
                    $updateData = [
                        'status_approval' => 'approved',
                        'approved_by_level2' => $user->id,
                        'approved_at_level2' => now(),
                        'approved_by' => $user->id, // backward compatibility
                        'approved_at' => now(), // backward compatibility
                    ];
                    
                    $updated = DB::table($tableName)
                        ->where('id', $itemId)
                        ->update($updateData);
                    
                    if ($updated === false || $updated === 0) {
                        Log::error('Failed to update approve level 2', [
                            'table' => $tableName,
                            'id' => $itemId,
                            'data' => $updateData
                        ]);
                        return [
                            'success' => false,
                            'message' => 'Gagal mengupdate status! Silakan coba lagi.'
                        ];
                    }
                    
                    $item->refresh();
                    
                    return [
                        'success' => true,
                        'message' => 'Data berhasil diapprove di Level 2 (KSPI)!'
                    ];
                } catch (\Exception $e) {
                    Log::error('Error approving at level 2: ' . $e->getMessage());
                    return [
                        'success' => false,
                        'message' => 'Terjadi kesalahan saat approve data: ' . $e->getMessage()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Status tidak valid untuk approval! Status saat ini: ' . $item->status_approval . ', User: ' . ($isAsmanKspi ? 'ASMAN KSPI' : ($isKspi ? 'KSPI' : 'Unknown'))
            ];
        }

        if ($action === 'reject') {
            if (!$rejectionReason || strlen(trim($rejectionReason)) < 10) {
                return [
                    'success' => false,
                    'message' => 'Alasan penolakan harus diisi minimal 10 karakter!'
                ];
            }

            // Level 1 Reject (ASMAN KSPI)
            if ($isAsmanKspi && $item->status_approval === 'pending') {
                try {
                    $tableName = $item->getTable();
                    $itemId = $item->getKey();
                    
                    // Check if level fields exist in database
                    $columns = DB::select("SHOW COLUMNS FROM `{$tableName}` LIKE 'rejected_by_level1'");
                    if (empty($columns)) {
                        Log::error('Level 1 fields not found in table', ['table' => $tableName]);
                        // Fallback: use old fields if new fields don't exist
                        $updateData = [
                            'status_approval' => 'rejected',
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                            'rejection_reason' => trim($rejectionReason),
                        ];
                    } else {
                        $updateData = [
                            'status_approval' => 'rejected_level1',
                            'rejected_by_level1' => $user->id,
                            'rejected_at_level1' => now(),
                            'rejection_reason_level1' => trim($rejectionReason),
                            'rejection_reason' => trim($rejectionReason), // backward compatibility
                        ];
                    }
                    
                    Log::info('Updating reject level 1', [
                        'table' => $tableName,
                        'id' => $itemId,
                        'data' => $updateData
                    ]);
                    
                    // Update menggunakan DB::table untuk memastikan berfungsi
                    $updated = DB::table($tableName)
                        ->where('id', $itemId)
                        ->update($updateData);
                    
                    Log::info('Update result', ['updated' => $updated, 'table' => $tableName, 'id' => $itemId]);
                    
                    if ($updated === false) {
                        Log::error('Failed to update reject level 1 - returned false', [
                            'table' => $tableName,
                            'id' => $itemId,
                            'data' => $updateData
                        ]);
                        return [
                            'success' => false,
                            'message' => 'Gagal mengupdate status! Silakan coba lagi.'
                        ];
                    }
                    
                    // Refresh model untuk memastikan data ter-update
                    $item->refresh();
                    
                    // Verify update
                    $updatedItem = DB::table($tableName)->where('id', $itemId)->first();
                    Log::info('Updated item status', [
                        'id' => $itemId,
                        'status_approval' => $updatedItem->status_approval ?? 'not found'
                    ]);
                    
                    return [
                        'success' => true,
                        'message' => 'Data berhasil ditolak di Level 1 (ASMAN KSPI) dengan alasan: ' . $rejectionReason
                    ];
                } catch (\Exception $e) {
                    Log::error('Error rejecting at level 1: ' . $e->getMessage(), [
                        'table' => $item->getTable(),
                        'id' => $item->getKey(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Terjadi kesalahan saat menolak data: ' . $e->getMessage()
                    ];
                }
            }

            // Level 2 Reject (KSPI) - bisa reject dari pending, approved_level1, atau rejected_level1 (berjenjang)
            if ($isKspi && in_array($item->status_approval, ['pending', 'approved_level1', 'rejected_level1'])) {
                try {
                    $tableName = $item->getTable();
                    $itemId = $item->getKey();
                    
                    // Simpan status sebelumnya untuk menentukan pesan
                    $previousStatus = $item->status_approval;
                    $isRejectBerjenjang = ($previousStatus === 'rejected_level1');
                    
                    // Check if level fields exist in database
                    $columns = DB::select("SHOW COLUMNS FROM `{$tableName}` LIKE 'rejected_by_level2'");
                    if (empty($columns)) {
                        Log::error('Level 2 fields not found in table', ['table' => $tableName]);
                        // Fallback: use old fields if new fields don't exist
                        $updateData = [
                            'status_approval' => 'rejected',
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                            'rejection_reason' => trim($rejectionReason),
                        ];
                    } else {
                        $updateData = [
                            'status_approval' => 'rejected',
                            'rejected_by_level2' => $user->id,
                            'rejected_at_level2' => now(),
                            'rejection_reason_level2' => trim($rejectionReason),
                            'rejection_reason' => trim($rejectionReason), // backward compatibility
                        ];
                    }
                    
                    Log::info('Updating reject level 2', [
                        'table' => $tableName,
                        'id' => $itemId,
                        'previous_status' => $previousStatus,
                        'is_berjenjang' => $isRejectBerjenjang,
                        'data' => $updateData
                    ]);
                    
                    // Update menggunakan DB::table untuk memastikan berfungsi
                    $updated = DB::table($tableName)
                        ->where('id', $itemId)
                        ->update($updateData);
                    
                    Log::info('Update result', ['updated' => $updated, 'table' => $tableName, 'id' => $itemId]);
                    
                    if ($updated === false) {
                        Log::error('Failed to update reject level 2 - returned false', [
                            'table' => $tableName,
                            'id' => $itemId,
                            'data' => $updateData
                        ]);
                        return [
                            'success' => false,
                            'message' => 'Gagal mengupdate status! Silakan coba lagi.'
                        ];
                    }
                    
                    // Refresh model untuk memastikan data ter-update
                    $item->refresh();
                    
                    // Verify update
                    $updatedItem = DB::table($tableName)->where('id', $itemId)->first();
                    Log::info('Updated item status', [
                        'id' => $itemId,
                        'status_approval' => $updatedItem->status_approval ?? 'not found'
                    ]);
                    
                    // Pesan berbeda untuk reject berjenjang vs reject langsung
                    if ($isRejectBerjenjang) {
                        $message = 'Data berhasil ditolak di Level 2 (KSPI) setelah reject Level 1 dengan alasan: ' . $rejectionReason;
                    } else {
                        $message = 'Data berhasil ditolak di Level 2 (KSPI) dengan alasan: ' . $rejectionReason;
                    }
                    
                    return [
                        'success' => true,
                        'message' => $message
                    ];
                } catch (\Exception $e) {
                    Log::error('Error rejecting at level 2: ' . $e->getMessage(), [
                        'table' => $item->getTable(),
                        'id' => $item->getKey(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Terjadi kesalahan saat menolak data: ' . $e->getMessage()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Status tidak valid untuk reject! Status saat ini: ' . $item->status_approval . ', User: ' . ($isAsmanKspi ? 'ASMAN KSPI' : ($isKspi ? 'KSPI' : 'Unknown'))
            ];
        }

        return [
            'success' => false,
            'message' => 'Aksi tidak valid!'
        ];
    }
}

