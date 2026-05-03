<?php
require __DIR__ . '/vendor/autoload.php';
$app = new think\App();
echo "Public Path: " . public_path() . "\n";
echo "Root Path: " . root_path() . "\n";
echo "App Path: " . app_path() . "\n";
