<?php

require 'vendor/autoload.php';

// Load environment variables from .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize DevCycleManager
$devCycleManager = new DevCycle\DevCycleManager(getenv('DEV_CYCLE_SDK_KEY'));

// Use feature flags for conditional rendering
$featureFlag = $devCycleManager->getFeatureFlag('your_feature_flag_key');

if ($featureFlag->isEnabled()) {
    // Render the feature component
    echo 'Feature is enabled!';
} else {
    // Fallback behavior
    echo 'Feature is not enabled.';
}

// Error handling
try {
    // Your existing code
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    echo 'An error occurred. Please try again later.';
}