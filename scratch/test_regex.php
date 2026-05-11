<?php

$origPara = '<w:p><w:pPr><w:spacing w:after="200"/></w:pPr><w:r><w:t>##RUANG_LINGKUP##</w:t></w:r><w:r><w:br w:type="page"/></w:r></w:p>';

$origPPr = '<w:pPr><w:spacing w:after="200"/></w:pPr>';
$numberedPPr = '<w:pPr><w:numPr><w:numId w:val="98"/></w:numPr></w:pPr>';
$rlRPr = '<w:rPr><w:sz w:val="22"/></w:rPr>';
$ruangLingkup = ['Item 1', 'Item 2'];

$injectedXml = '</w:t></w:r></w:p>'; // Tutup paragraf aslinya

foreach ($ruangLingkup as $rl) {
    $injectedXml .= '<w:p>'
        . $numberedPPr
        . '<w:r>' . $rlRPr . '<w:t>' . htmlspecialchars($rl) . '</w:t></w:r>'
        . '</w:p>';
}

// Buka paragraf lagi untuk sisa teks di bawahnya (menggunakan PPr asli, tanpa nomor!)
$injectedXml .= '<w:p>' . $origPPr . '<w:r><w:t>';

$newPara = str_replace('##RUANG_LINGKUP##', $injectedXml, $origPara);

echo $newPara;
