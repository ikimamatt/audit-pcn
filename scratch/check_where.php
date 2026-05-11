<?php
$zip = new ZipArchive();
if ($zip->open('Template Program Kerja Audit.docx') === TRUE) {
    for($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        if(strpos($filename, '.xml') !== false) {
            $xml = $zip->getFromName($filename);
            if(strpos($xml, 'NOMOR_PKA') !== false) {
                echo "Found in: $filename\n";
            }
        }
    }
    $zip->close();
}
