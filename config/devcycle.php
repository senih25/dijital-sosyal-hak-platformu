<?php

class DevCycleManager {
    private $projectKey;
    private $sdkKey;
    private $sdk;

    public function __construct($projectKey, $sdkKey) {
        $this->projectKey = $projectKey;
        $this->sdkKey = $sdkKey;
        $this->initializeSDK();
    }

    private function initializeSDK() {
        // Assume DevCycle SDK is included and initialized
        try {
            $this->sdk = new DevCycleSDK($this->projectKey, $this->sdkKey);
        } catch (Exception $e) {
            error_log('SDK initialization failed: ' . $e->getMessage());
            throw new Exception('Failed to initialize SDK.');
        }
    }

    public function isFeatureEnabled($featureKey, $user) {
        try {
            return $this->sdk->isFeatureEnabled($featureKey, $user);
        } catch (Exception $e) {
            error_log('Error in checking feature: ' . $e->getMessage());
            return false;
        }
    }

    public function getVariation($featureKey, $user) {
        try {
            return $this->sdk->getVariation($featureKey, $user);
        } catch (Exception $e) {
            error_log('Error in getting variation: ' . $e->getMessage());
            return null;
        }
    }

    public function trackEvent($eventName, $user) {
        try {
            $this->sdk->trackEvent($eventName, $user);
        } catch (Exception $e) {
            error_log('Error in tracking event: ' . $e->getMessage());
        }
    }
} 
