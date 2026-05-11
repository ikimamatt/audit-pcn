<?php
$zip = new ZipArchive();
if ($zip->open('Template Program Kerja Audit.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    preg_match_all('/<w:t>[^<]*\{[^<]*<\/w:t>.*PERIODE_AUDIT/U', $xml, $matches);
    echo "Is Split? " . (empty($matches[0]) ? "No" : "Yes") . "\n";
    $zip->close();
}
