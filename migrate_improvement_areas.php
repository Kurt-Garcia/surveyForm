#!/usr/bin/env php
<?php

/**
 * This script creates the new improvement areas tables 
 * and migrates the data from the old structure to the new one.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Starting database migration for survey improvement areas...\n";

// Run the migrations to create the new tables
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput(['artisan', 'migrate', '--path=database/migrations/2025_07_07_000000_create_survey_improvement_areas_table.php']),
    new Symfony\Component\Console\Output\ConsoleOutput
);

// Run the migration to create the new category table
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput(['artisan', 'migrate', '--path=database/migrations/2025_07_07_100000_create_survey_improvement_categories_table.php']),
    new Symfony\Component\Console\Output\ConsoleOutput
);

// Run the migration to create the new details table
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput(['artisan', 'migrate', '--path=database/migrations/2025_07_07_100001_create_survey_improvement_details_table.php']),
    new Symfony\Component\Console\Output\ConsoleOutput
);

// Run the migration to move data from old to new structure
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput(['artisan', 'migrate', '--path=database/migrations/2025_07_07_100002_migrate_survey_improvement_data.php']),
    new Symfony\Component\Console\Output\ConsoleOutput
);

echo "Migration completed.\n";

$kernel->terminate($input, $status);

exit($status);
