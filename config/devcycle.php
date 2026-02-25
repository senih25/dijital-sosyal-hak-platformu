<?php

class DevCycleManager {
    private $sdk;

    public function __construct() {
        // Initialize the DevCycle SDK
        try {
            // Assuming DevCycle SDK is available through autoload or manual include
            $this->sdk = new DevCycleSDK(); // Replace with actual SDK initialization
        } catch (Exception $e) {
            error_log('Error initializing DevCycle SDK: ' . $e->getMessage());
            throw new Exception('Failed to initialize the DevCycle SDK.');
        }
    }

    public function isFeatureEnabled($featureKey, $user) {
        try {
            return $this->sdk->isFeatureEnabled($featureKey, $user);
        } catch (Exception $e) {
            error_log('Error checking feature status: ' . $e->getMessage());
            return false; // Default to false on error
        }
    }

    public function getVariant($featureKey, $user) {
        try {
            return $this->sdk->getVariant($featureKey, $user);
        } catch (Exception $e) {
            error_log('Error getting feature variant: ' . $e->getMessage());
            return null; // Default to null on error
        }
    }
}

?>