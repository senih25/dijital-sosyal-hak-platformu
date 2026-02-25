<?php

class DevCycleManager
{
    private $client = null;
    private $initialized = false;

    public function __construct()
    {
        $apiKey = getenv('DEVCYCLE_API_KEY') ?: '';

        if (empty($apiKey)) {
            error_log('DevCycle: DEVCYCLE_API_KEY is not set.');
            return;
        }

        try {
            if (class_exists('\DevCycle\Api\DVCClient')) {
                $config = \DevCycle\Configuration::getDefaultConfiguration()
                    ->setApiKey('Authorization', $apiKey);
                $this->client = new \DevCycle\Api\DVCClient(null, $config);
                $this->initialized = true;
            }
        } catch (\Exception $e) {
            error_log('DevCycle: SDK initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if a feature flag is enabled for a given user.
     *
     * @param string $userId
     * @param string $flagKey
     * @return bool
     */
    public function isFeatureEnabled(string $userId, string $flagKey): bool
    {
        if (!$this->initialized || $this->client === null) {
            return false;
        }

        try {
            $userContext = new \DevCycle\Model\DVCUser(['user_id' => $userId]);
            $variables = $this->client->allVariables($userContext);
            if (isset($variables[$flagKey])) {
                return (bool) $variables[$flagKey]->getValue();
            }
        } catch (\Exception $e) {
            error_log('DevCycle: isFeatureEnabled error for flag "' . $flagKey . '": ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Get the variant value for an A/B experiment.
     *
     * @param string $userId
     * @param string $experimentKey
     * @return string|null
     */
    public function getVariant(string $userId, string $experimentKey): ?string
    {
        if (!$this->initialized || $this->client === null) {
            return null;
        }

        try {
            $userContext = new \DevCycle\Model\DVCUser(['user_id' => $userId]);
            $variables = $this->client->allVariables($userContext);
            if (isset($variables[$experimentKey])) {
                return (string) $variables[$experimentKey]->getValue();
            }
        } catch (\Exception $e) {
            error_log('DevCycle: getVariant error for experiment "' . $experimentKey . '": ' . $e->getMessage());
        }

        return null;
    }
}
