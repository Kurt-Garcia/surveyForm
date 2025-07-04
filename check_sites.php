<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sites = App\Models\Site::with('sbu')->get();
foreach ($sites as $site) {
    echo $site->name . ' - SBU: ' . $site->sbu->name . PHP_EOL;
}
