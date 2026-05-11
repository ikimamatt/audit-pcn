<?php
$zip = new ZipArchive();
if ($zip->open('Template Program Kerja Audit.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    
    // Find index of NOMOR_PKA
    $pos = strpos(strip_tags($xml), 'NOMOR_PKA');
    if ($pos !== false) {
        $xml_stripped = strip_tags($xml);
        echo "Found text context: " . substr($xml_stripped, max(0, $pos - 20), 50) . "\n";
    }
    
    preg_match_all('/.{0,50}NOMOR_PKA.{0,50}/', $xml, $matches);
    print_r($matches);
    
    $zip->close();
}
