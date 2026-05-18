<?php
require 'c:/laragon/www/audit-pcn/vendor/autoload.php';
$app = require_once 'c:/laragon/www/audit-pcn/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\MasterData\MasterUser::with('akses')->get();
foreach ($users as $u) {
    $akses = optional($u->akses)->nama_akses ?? '(null)';
    echo $u->id . ': ' . $u->nama . ' => [' . $akses . ']' . PHP_EOL;
}
