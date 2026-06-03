<?php

namespace App\Services\Audit;

use App\Models\Models\Audit\ProgramKerjaAudit;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;
use ReflectionClass;

class PkaDocumentService
{
    /**
     * Generate the Word Document for PKA.
     * Returns the temporary file path of the generated document.
     *
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function generate(int $id): string
    {
        $item = ProgramKerjaAudit::with([
            'perencanaanAudit.auditee',
            'perencanaanAudit.koordinator',
            'perencanaanAudit.ketuaTim',
            'prosesBisnis.risikoList.kontrolList',
            'milestones',
            'dokumen',
        ])->findOrFail($id);

        $templatePath = base_path('Template Program Kerja Audit.docx');

        if (!file_exists($templatePath)) {
            throw new \FileNotFoundException('Template dokumen "Template Program Kerja Audit.docx" tidak ditemukan di root folder.');
        }

        // Format data
        $tanggalPka = $item->tanggal_pka ? Carbon::parse($item->tanggal_pka)->locale('id')->translatedFormat('d F Y') : '-';

        $tglMulai = $item->perencanaanAudit->tanggal_audit_mulai;
        $tglSampai = $item->perencanaanAudit->tanggal_audit_sampai;
        $waktuAudit = '-';
        if ($tglMulai && $tglSampai) {
            $mulai = Carbon::parse($tglMulai)->locale('id')->translatedFormat('d F Y');
            $sampai = Carbon::parse($tglSampai)->locale('id')->translatedFormat('d F Y');
            $waktuAudit = $mulai . ' s/d ' . $sampai;
        }

        $periode = $item->perencanaanAudit->periode_audit ?? '-';
        $nomorPka = $item->no_pka ?? '-';
        $nomorTugas = $item->perencanaanAudit->nomor_surat_tugas ?? '-';
        $tanggalTugas = $item->perencanaanAudit->tanggal_surat_tugas ? Carbon::parse($item->perencanaanAudit->tanggal_surat_tugas)->locale('id')->translatedFormat('d F Y') : '-';
        $judulPka = $item->judul_pka ?? '-';

        // Data Awal Yang Perlu Disiapkan
        $dataAwalDokumenRaw = is_array($item->data_awal_dokumen) ? $item->data_awal_dokumen : json_decode($item->data_awal_dokumen ?? '[]', true);
        $dataAwalDokumen = [];
        if (!empty($dataAwalDokumenRaw) && is_array($dataAwalDokumenRaw)) {
            foreach ($dataAwalDokumenRaw as $idx => $da) {
                $dataAwalDokumen[] = [
                    'no_da' => $idx + 1,
                    'nama_dokumen' => $da['nama_dokumen'] ?? '-',
                    'ruang_lingkup_da' => $da['ruang_lingkup'] ?? '-',
                    'periode_da' => $da['periode'] ?? '-'
                ];
            }
        } else {
            $dataAwalDokumen = [
                ['no_da' => 1, 'nama_dokumen' => '-', 'ruang_lingkup_da' => '-', 'periode_da' => '-']
            ];
        }

        // Ruang Lingkup Audit (JSON array dari perencanaan_audit)
        $ruangLingkup = $item->perencanaanAudit->ruang_lingkup ?? [];
        if (is_string($ruangLingkup)) {
            $ruangLingkup = json_decode($ruangLingkup, true) ?? [];
        }

        // Proses template menggunakan PhpWord
        $templateProcessor = new TemplateProcessor($templatePath);

        // Replace placeholders
        $templateProcessor->setValue('PERIODE_AUDIT', $periode);
        $templateProcessor->setValue('WAKTU_AUDIT', $waktuAudit);
        $templateProcessor->setValue('NOMOR_PKA', $nomorPka);
        $templateProcessor->setValue('TANGGAL_PKA', $tanggalPka);
        $templateProcessor->setValue('NOMOR_TUGAS', $nomorTugas);
        $templateProcessor->setValue('TANGGAL_TUGAS', $tanggalTugas);
        $templateProcessor->setValue('TANGGAL_SURAT_TUGAS', $tanggalTugas);
        $templateProcessor->setValue('JUDUL_PKA', $judulPka);
        $templateProcessor->setValue('RUANG_LINGKUP_AUDIT', '##RUANG_LINGKUP##');
        
        $templateProcessor->setValue('KOORDINATOR_NAMA', $item->perencanaanAudit->koordinator->nama ?? '-');
        $templateProcessor->setValue('KOORDINATOR_NIP', $item->perencanaanAudit->koordinator->nip ?? '-');
        $templateProcessor->setValue('KETUA_TIM_NAMA', $item->perencanaanAudit->ketuaTim->nama ?? '-');
        $templateProcessor->setValue('KETUA_TIM_NIP', $item->perencanaanAudit->ketuaTim->nip ?? '-');

        // Clone baris data awal
        try {
            $templateProcessor->cloneRowAndSetValues('no_da', $dataAwalDokumen);
        } catch (\Exception $e) {
            // Abaikan jika template belum punya variabel no_da
        }
        
        // Siapkan Data Tim Pemeriksa
        $timPemeriksa = [];
        $noTim = 1;

        // 1. Koordinator
        if ($item->perencanaanAudit->koordinator) {
            $timPemeriksa[] = [
                'no_tim' => $noTim++,
                'nama_tim' => $item->perencanaanAudit->koordinator->nama,
                'role_tim' => 'Koordinator',
                'nip_tim' => $item->perencanaanAudit->koordinator->nip,
            ];
        }

        // 2. Ketua Tim
        if ($item->perencanaanAudit->ketuaTim) {
            $timPemeriksa[] = [
                'no_tim' => $noTim++,
                'nama_tim' => $item->perencanaanAudit->ketuaTim->nama,
                'role_tim' => 'Ketua Tim',
                'nip_tim' => $item->perencanaanAudit->ketuaTim->nip,
            ];
        }

        // 3. Anggota
        $auditors = is_array($item->perencanaanAudit->auditor) ? $item->perencanaanAudit->auditor : json_decode($item->perencanaanAudit->auditor ?? '[]', true);
        if (is_array($auditors)) {
            foreach ($auditors as $auditorString) {
                if (preg_match('/^(.*?)\s*-\s*NIP:\s*(.*)$/', $auditorString, $matches)) {
                    $timPemeriksa[] = [
                        'no_tim' => $noTim++,
                        'nama_tim' => trim($matches[1]),
                        'role_tim' => 'Anggota',
                        'nip_tim' => trim($matches[2]),
                    ];
                } else {
                    $timPemeriksa[] = [
                        'no_tim' => $noTim++,
                        'nama_tim' => $auditorString,
                        'role_tim' => 'Anggota',
                        'nip_tim' => '-',
                    ];
                }
            }
        }

        if (empty($timPemeriksa)) {
            $timPemeriksa[] = [
                'no_tim' => 1,
                'nama_tim' => '-',
                'role_tim' => '-',
                'nip_tim' => '-',
            ];
        }

        try {
            $templateProcessor->cloneRowAndSetValues('no_tim', $timPemeriksa);
        } catch (\Exception $e) {
            // Abaikan jika tidak ada variabel no_tim
        }
        
        // Format format tanggal milestone untuk Word
        $formatDateRange = function($ms) {
            if (!$ms || (!$ms->tanggal_mulai && !$ms->tanggal_selesai)) return '-';
            if ($ms->tanggal_mulai && !$ms->tanggal_selesai) return Carbon::parse($ms->tanggal_mulai)->locale('id')->translatedFormat('d F Y');
            if (!$ms->tanggal_mulai && $ms->tanggal_selesai) return Carbon::parse($ms->tanggal_selesai)->locale('id')->translatedFormat('d F Y');
            
            $mulai = Carbon::parse($ms->tanggal_mulai)->locale('id');
            $selesai = Carbon::parse($ms->tanggal_selesai)->locale('id');
            
            if ($mulai->format('Y-m-d') === $selesai->format('Y-m-d')) {
                return $mulai->translatedFormat('d F Y');
            }
            
            if ($mulai->format('Y') === $selesai->format('Y')) {
                return $mulai->translatedFormat('d F') . ' s.d ' . $selesai->translatedFormat('d F Y');
            }
            
            return $mulai->translatedFormat('d F Y') . ' s.d ' . $selesai->translatedFormat('d F Y');
        };

        // Set Template Variables for Milestones
        $templateProcessor->setValue('MS_PERMINTAAN_DOKUMEN', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Surat Permintaan Dokumen kepada Auditee')));
        $templateProcessor->setValue('MS_EKSPOSE_PKA', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Ekspose PKA Internal')));
        $templateProcessor->setValue('MS_ENTRY_MEETING', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Entry Meeting')));
        $templateProcessor->setValue('MS_WALKTHROUGH', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Walkthrough')));
        $templateProcessor->setValue('MS_TOD', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'TOD')));
        $templateProcessor->setValue('MS_TOE', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'TOE')));
        $templateProcessor->setValue('MS_DRAF_LHA', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Draf LHA')));
        $templateProcessor->setValue('MS_PRA_EXIT', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Pra Exit Meeting untuk Finalisasi LHA')));
        $templateProcessor->setValue('MS_EXIT_MEETING', $formatDateRange($item->milestones->firstWhere('nama_milestone', 'Exit Meeting')));

        // Set DAFTAR_AUDITOR langsung
        $anggotaList = collect($timPemeriksa)->where('role_tim', 'Anggota')->values();
        if ($anggotaList->count() > 0) {
            $auditorLines = $anggotaList->map(function($a, $i) {
                return ($i + 1) . '. ' . $a['nama_tim'];
            })->toArray();
            $auditorText = implode('</w:t><w:br/><w:t>', $auditorLines);
        } else {
            $auditorText = '-';
        }
        $templateProcessor->setValue('DAFTAR_AUDITOR', $auditorText);

        $pbHierarki = $item->prosesBisnis->sortBy('urutan');

        // Bangun flat rows
        $flatRows = [];
        $noGlobal = 1;
        foreach ($pbHierarki as $pb) {
            $risikoList = $pb->risikoList->sortBy('urutan');
            $risikoCount = max($risikoList->count(), 1);

            if ($risikoList->isEmpty()) {
                $flatRows[] = [
                    'NO'               => $noGlobal++,
                    'PROSES_BISNIS'    => $pb->nama_proses_bisnis ?? '-',
                    'DESKRIPSI_RISIKO' => '-',
                    'is_first_of_pb'  => true,
                    'pb_span'          => 1,
                ];
            } else {
                $first = true;
                foreach ($risikoList as $risiko) {
                    $flatRows[] = [
                        'NO'               => $first ? $noGlobal++ : '',
                        'PROSES_BISNIS'    => $pb->nama_proses_bisnis ?? '-',
                        'DESKRIPSI_RISIKO' => $risiko->deskripsi_risiko ?? '-',
                        'is_first_of_pb'  => $first,
                        'pb_span'          => $risikoCount,
                    ];
                    $first = false;
                }
            }
        }

        if (empty($flatRows)) {
            $flatRows = [[
                'NO'               => 1,
                'PROSES_BISNIS'    => '-',
                'DESKRIPSI_RISIKO' => '-',
                'is_first_of_pb'  => true,
                'pb_span'          => 1,
            ]];
        }

        $vMergeFirst   = '##VMERGE_FIRST##';
        $vMergeCont    = '##VMERGE_CONT##';
        $noVMergeFirst = '##NO_VMERGE_FIRST##';
        $noVMergeCont  = '##NO_VMERGE_CONT##';

        $tableData = array_map(fn($r) => [
            'NO'               => ($r['is_first_of_pb'] && $r['pb_span'] > 1)
                                    ? $noVMergeFirst . (string)($r['NO'] ?? '')
                                    : ((!$r['is_first_of_pb'])
                                        ? $noVMergeCont
                                        : (string)($r['NO'] ?? '')),
            'PROSES_BISNIS'    => ($r['is_first_of_pb'] && $r['pb_span'] > 1)
                                    ? $vMergeFirst . htmlspecialchars($r['PROSES_BISNIS'])
                                    : ((!$r['is_first_of_pb'])
                                        ? $vMergeCont . htmlspecialchars($r['PROSES_BISNIS'])
                                        : $r['PROSES_BISNIS']),
            'DESKRIPSI_RISIKO' => $r['DESKRIPSI_RISIKO'],
        ], $flatRows);

        try {
            $templateProcessor->cloneRowAndSetValues('NO', $tableData);
        } catch (\Exception $e) {
            \Log::error('CloneRowAndSetValues Error: ' . $e->getMessage());
        }

        // Tabel Risk Assessment (RA)
        $raTableData = [];
        foreach ($pbHierarki as $pb) {
            foreach ($pb->risikoList->sortBy('urutan') as $risiko) {
                $kontrolText = '';
                if ($risiko->kontrolList->count() > 0) {
                    $kList = [];
                    foreach ($risiko->kontrolList->sortBy('urutan') as $kIdx => $kontrol) {
                        $kList[] = ($kIdx + 1) . '. ' . $kontrol->deskripsi_kontrol;
                    }
                    $kontrolText = implode("\n", $kList);
                } else {
                    $kontrolText = '-';
                }

                $raTableData[] = [
                    'RA_RISIKO'  => $risiko->deskripsi_risiko ?? '-',
                    'RA_LEVEL'   => $risiko->level_risiko ?? '-',
                    'RA_KONTROL' => $kontrolText,
                ];
            }
        }

        if (empty($raTableData)) {
            $raTableData[] = [
                'RA_RISIKO'  => '-',
                'RA_LEVEL'   => '-',
                'RA_KONTROL' => '-',
            ];
        }

        try {
            $templateProcessor->cloneRowAndSetValues('RA_RISIKO', $raTableData);
        } catch (\Exception $e) {
            \Log::error('CloneRowAndSetValues Risk Assessment Error: ' . $e->getMessage());
        }

        $apNoMarker = '##AP_NO_DATA##';
        $apPbMarker = '##AP_PB_DATA##';
        $templateProcessor->setValue('AP_NO', $apNoMarker);
        $templateProcessor->setValue('AP_PROSES_BISNIS', $apPbMarker);

        // XML Post-processing using reflection
        $refClass = new ReflectionClass(get_class($templateProcessor));
        $mainPart  = $refClass->getProperty('tempDocumentMainPart');
        $mainPart->setAccessible(true);
        $xml = $mainPart->getValue($templateProcessor);

        $prosesBisnisList = $pbHierarki->pluck('nama_proses_bisnis')->toArray();

        $xml = preg_replace_callback(
            '/<w:tc[^>]*>.*?<\/w:tc>/s',
            function ($match) use ($vMergeFirst, $vMergeCont, $noVMergeFirst, $noVMergeCont, $apNoMarker, $apPbMarker, $prosesBisnisList) {
                $cellXml = $match[0];

                if (strpos($cellXml, $noVMergeFirst) !== false) {
                    if (strpos($cellXml, '<w:tcPr') !== false) {
                        $cellXml = preg_replace('/<w:tcPr([^>]*)>/', '<w:tcPr$1><w:vMerge w:val="restart"/>', $cellXml, 1);
                    }
                    $cellXml = str_replace(htmlspecialchars($noVMergeFirst), '', $cellXml);
                    $cellXml = str_replace($noVMergeFirst, '', $cellXml);

                } elseif (strpos($cellXml, $noVMergeCont) !== false) {
                    if (strpos($cellXml, '<w:tcPr') !== false) {
                        $cellXml = preg_replace('/<w:tcPr([^>]*)>/', '<w:tcPr$1><w:vMerge/>', $cellXml, 1);
                    }
                    $tcPrEnd = strpos($cellXml, '</w:tcPr>');
                    if ($tcPrEnd !== false) {
                        $cellXml = substr($cellXml, 0, $tcPrEnd + strlen('</w:tcPr>')) . '<w:p/></w:tc>';
                    }

                } elseif (strpos($cellXml, $vMergeFirst) !== false) {
                    if (strpos($cellXml, '<w:tcPr') !== false) {
                        $cellXml = preg_replace('/<w:tcPr([^>]*)>/', '<w:tcPr$1><w:vMerge w:val="restart"/>', $cellXml, 1);
                    }
                    $cellXml = str_replace(htmlspecialchars($vMergeFirst), '', $cellXml);
                    $cellXml = str_replace($vMergeFirst, '', $cellXml);

                } elseif (strpos($cellXml, $vMergeCont) !== false) {
                    if (strpos($cellXml, '<w:tcPr') !== false) {
                        $cellXml = preg_replace('/<w:tcPr([^>]*)>/', '<w:tcPr$1><w:vMerge/>', $cellXml, 1);
                    }
                    $tcPrEnd = strpos($cellXml, '</w:tcPr>');
                    if ($tcPrEnd !== false) {
                        $cellXml = substr($cellXml, 0, $tcPrEnd + strlen('</w:tcPr>')) . '<w:p/></w:tc>';
                    }

                } elseif (strpos($cellXml, $apNoMarker) !== false) {
                    $tcPrEnd = strpos($cellXml, '</w:tcPr>');
                    if ($tcPrEnd !== false) {
                        $cellXml = substr($cellXml, 0, $tcPrEnd + strlen('</w:tcPr>')) . '<w:p/></w:tc>';
                    }

                } elseif (strpos($cellXml, $apPbMarker) !== false) {
                    $rPr = '';
                    if (preg_match('/<w:rPr>.*?<\/w:rPr>/s', $cellXml, $m)) {
                        $rPr = $m[0];
                    }
                    $pbParagraphs = '';
                    if (count($prosesBisnisList) > 0) {
                        foreach ($prosesBisnisList as $i => $pb) {
                            $text = ($i + 1) . '. ' . htmlspecialchars($pb);
                            $pbParagraphs .= '<w:p>'
                                . '<w:pPr><w:spacing w:after="200"/></w:pPr>'
                                . '<w:r>' . $rPr . '<w:t xml:space="preserve">' . $text . '</w:t></w:r>'
                                . '</w:p>';
                        }
                    } else {
                        $pbParagraphs = '<w:p><w:r>' . $rPr . '<w:t>-</w:t></w:r></w:p>';
                    }
                    $tcPrEnd = strpos($cellXml, '</w:tcPr>');
                    if ($tcPrEnd !== false) {
                        $cellXml = substr($cellXml, 0, $tcPrEnd + strlen('</w:tcPr>')) . $pbParagraphs . '</w:tc>';
                    }
                }

                return $cellXml;
            },
            $xml
        );

        // Ruang Lingkup Audit auto-numbering
        $rlMarker = '##RUANG_LINGKUP##';
        if (strpos($xml, $rlMarker) !== false) {
            if (preg_match('/<w:p\b(?:(?!<w:p\b).)*?' . preg_quote($rlMarker, '/') . '.*?<\/w:p>/s', $xml, $pMatch)) {
                $origPara = $pMatch[0];
                
                $rlRPr = '';
                if (preg_match('/<w:rPr>.*?<\/w:rPr>/s', $origPara, $rlMatch)) {
                    $rlRPr = $rlMatch[0];
                    $rlRPr = preg_replace('/<w:sz[^\/]*\/?>/', '', $rlRPr);
                    $rlRPr = preg_replace('/<w:szCs[^\/]*\/?>/', '', $rlRPr);
                    $rlRPr = preg_replace('/<w:rFonts[^\/]*\/?>/', '', $rlRPr);
                    $rlRPr = str_replace('</w:rPr>', '<w:rFonts w:ascii="Tahoma" w:hAnsi="Tahoma" w:cs="Tahoma" w:eastAsia="Tahoma"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>', $rlRPr);
                } else {
                    $rlRPr = '<w:rPr><w:rFonts w:ascii="Tahoma" w:hAnsi="Tahoma" w:cs="Tahoma" w:eastAsia="Tahoma"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>';
                }
                
                $origPPr = '';
                if (preg_match('/<w:pPr>.*?<\/w:pPr>/s', $origPara, $pPrMatch)) {
                    $origPPr = $pPrMatch[0];
                }
                
                $numberedPPr = $origPPr;
                if ($numberedPPr !== '') {
                    $numberedPPr = preg_replace('/<w:jc[^\/]*\/?>/', '', $numberedPPr);
                    $numberedPPr = preg_replace('/<w:numPr>.*?<\/w:numPr>/s', '', $numberedPPr);
                    $numberedPPr = preg_replace('/<w:ind[^\/]*\/?>/', '', $numberedPPr);
                    $numberedPPr = str_replace('</w:pPr>', '<w:numPr><w:ilvl w:val="0"/><w:numId w:val="98"/></w:numPr><w:jc w:val="both"/><w:ind w:left="1000" w:hanging="360"/></w:pPr>', $numberedPPr);
                } else {
                    $numberedPPr = '<w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="98"/></w:numPr><w:jc w:val="both"/><w:ind w:left="1000" w:hanging="360"/></w:pPr>';
                }
                
                $injectedXml = '</w:t></w:r></w:p>'; 
                
                if (!empty($ruangLingkup)) {
                    foreach ($ruangLingkup as $rl) {
                        $injectedXml .= '<w:p>'
                            . $numberedPPr
                            . '<w:r>' . $rlRPr . '<w:t>' . htmlspecialchars($rl) . '</w:t></w:r>'
                            . '</w:p>';
                    }
                } else {
                    $injectedXml .= '<w:p>' . $numberedPPr . '<w:r>' . $rlRPr . '<w:t>-</w:t></w:r></w:p>';
                }
                
                $injectedXml .= '<w:p>' . $origPPr . '<w:r><w:t>';
                
                $newPara = str_replace($rlMarker, $injectedXml, $origPara);
                $xml = str_replace($origPara, $newPara, $xml);
            }
        }

        $mainPart->setValue($templateProcessor, $xml);

        $tempPath = storage_path('app/public/temp_' . uniqid() . '.docx');
        $templateProcessor->saveAs($tempPath);

        // Inject Word native numbering definitions into zip
        $zip = new ZipArchive();
        if ($zip->open($tempPath) === true) {
            $numberingXml = $zip->getFromName('word/numbering.xml');

            $ns = 'xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"';
            $abstractNum = '<w:abstractNum w:abstractNumId="99" ' . $ns . '>'
                . '<w:lvl w:ilvl="0">'
                . '<w:start w:val="1"/>'
                . '<w:numFmt w:val="decimal"/>'
                . '<w:lvlText w:val="%1."/>'
                . '<w:lvlJc w:val="left"/>'
                . '<w:pPr><w:ind w:left="360" w:hanging="360"/></w:pPr>'
                . '<w:rPr><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>'
                . '</w:lvl>'
                . '</w:abstractNum>';
            $numRef = '<w:num w:numId="99" ' . $ns . '><w:abstractNumId w:val="99"/></w:num>';

            if ($numberingXml !== false) {
                $abstractNumClean = '<w:abstractNum w:abstractNumId="99">'
                    . '<w:lvl w:ilvl="0">'
                    . '<w:start w:val="1"/>'
                    . '<w:numFmt w:val="decimal"/>'
                    . '<w:lvlText w:val="%1."/>'
                    . '<w:lvlJc w:val="left"/>'
                    . '<w:pPr><w:ind w:left="360" w:hanging="360"/></w:pPr>'
                    . '<w:rPr><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>'
                    . '</w:lvl>'
                    . '</w:abstractNum>';
                $numRefClean = '<w:num w:numId="99"><w:abstractNumId w:val="99"/></w:num>';

                $abstractNumRL = '<w:abstractNum w:abstractNumId="98">'
                    . '<w:lvl w:ilvl="0">'
                    . '<w:start w:val="1"/>'
                    . '<w:numFmt w:val="decimal"/>'
                    . '<w:lvlText w:val="%1."/>'
                    . '<w:lvlJc w:val="left"/>'
                    . '<w:pPr><w:ind w:left="1000" w:hanging="360"/></w:pPr>'
                    . '<w:rPr><w:rFonts w:ascii="Tahoma" w:hAnsi="Tahoma" w:cs="Tahoma" w:eastAsia="Tahoma"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>'
                    . '</w:lvl>'
                    . '</w:abstractNum>';
                $numRefRL = '<w:num w:numId="98"><w:abstractNumId w:val="98"/></w:num>';

                if (preg_match('/<w:num\s/', $numberingXml)) {
                    $numberingXml = preg_replace('/<w:num\s/', $abstractNumClean . $abstractNumRL . '<w:num ', $numberingXml, 1);
                } else {
                    $numberingXml = str_replace('</w:numbering>', $abstractNumClean . $abstractNumRL . '</w:numbering>', $numberingXml);
                }
                $numberingXml = str_replace('</w:numbering>', $numRefClean . $numRefRL . '</w:numbering>', $numberingXml);
            } else {
                $numberingXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                    . '<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
                    . $abstractNum . $numRef
                    . '</w:numbering>';

                $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
                if ($relsXml !== false && strpos($relsXml, 'numbering.xml') === false) {
                    $relsXml = str_replace(
                        '</Relationships>',
                        '<Relationship Id="rIdNum99" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/></Relationships>',
                        $relsXml
                    );
                    $zip->addFromString('word/_rels/document.xml.rels', $relsXml);
                }

                $contentTypes = $zip->getFromName('[Content_Types].xml');
                if ($contentTypes !== false && strpos($contentTypes, 'numbering.xml') === false) {
                    $contentTypes = str_replace(
                        '</Types>',
                        '<Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/></Types>',
                        $contentTypes
                    );
                    $zip->addFromString('[Content_Types].xml', $contentTypes);
                }
            }

            $zip->addFromString('word/numbering.xml', $numberingXml);
            $zip->close();
        }

        return $tempPath;
    }
}
