<?php

// This script helps to fix migration issues by checking the format of migration files
// and ensuring they follow Laravel's structure with proper class declarations

require 'vendor/autoload.php';

use Illuminate\Support\Facades\File;

// Get all migration files
$migrationPath = __DIR__ . '/database/migrations';
$files = glob($migrationPath . '/*.php');

$issues = [];
$classNames = [];
$duplicateClasses = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    $fileName = basename($file);
    
    // Check if file has proper namespace and class structure
    $hasClass = preg_match('/class\s+([a-zA-Z0-9_]+)\s+extends\s+Migration/i', $content, $matches);
    
    if (!$hasClass) {
        echo "Issue found in {$fileName}: No proper migration class found\n";
        $issues[] = $file;
    } else {
        $className = $matches[1];
        
        // Check for duplicate class names
        if (isset($classNames[$className])) {
            echo "DUPLICATE CLASS found in {$fileName}: Class {$className} is already defined in {$classNames[$className]}\n";
            $duplicateClasses[] = [
                'className' => $className,
                'files' => [$classNames[$className], $file]
            ];
        } else {
            $classNames[$className] = $fileName;
            echo "OK: {$fileName} has class {$className}\n";
        }
    }
}

echo "\n";
echo count($issues) . " format issues found.\n";
echo count($duplicateClasses) . " duplicate class issues found.\n";

if (count($issues) > 0 || count($duplicateClasses) > 0) {
    echo "\nRecommendations:\n";
    
    if (count($issues) > 0) {
        echo "- Fix migration files with improper format.\n";
    }
    
    if (count($duplicateClasses) > 0) {
        echo "- Fix duplicate class issues by renaming classes or removing duplicate files.\n";
        
        foreach ($duplicateClasses as $duplicate) {
            echo "  * Class '{$duplicate['className']}' is defined in these files:\n";
            foreach ($duplicate['files'] as $file) {
                echo "    - " . basename($file) . "\n";
            }
        }
    }
}

echo "\nTo run the fixed migrations, use:\n";
echo "php artisan migrate\n";
