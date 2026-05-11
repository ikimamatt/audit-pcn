<?php
require 'vendor/autoload.php';
$phpWord = new \PhpOffice\PhpWord\PhpWord();
$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('Template Program Kerja Audit.docx');

$table = new \PhpOffice\PhpWord\Element\Table(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
$table->addRow();
$table->addCell(500)->addText('1');
$table->addCell(2000)->addText('PB 1');
$cell = $table->addCell(2000, ['vMerge' => 'restart']);
$cell->addListItem('Risk 1', 0, null, \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER);
$cell->addListItem('Risk 2', 0, null, \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER);

$table->addRow();
$table->addCell(500)->addText('2');
$table->addCell(2000)->addText('PB 2');
$table->addCell(2000, ['vMerge' => 'continue']);

// Kita cek apakah setComplexBlock ada dan bisa terima table
try {
    $templateProcessor->setComplexBlock('TABLE_PKA', $table);
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
