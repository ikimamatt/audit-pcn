<?php
$zip = new ZipArchive();
if ($zip->open('Template Program Kerja Audit.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    preg_match_all('/\{\{.*\}\}/U', strip_tags($xml), $matches);
    print_r($matches);
    $zip->close();
} else {
    echo "Failed to open zip";
}
