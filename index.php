<?php

require 'vendor/autoload.php';

// Environment variables (e.g. DEV_CYCLE_SDK_KEY) are expected to be set by the server environment.

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