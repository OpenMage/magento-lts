<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core Environment helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_EnvironmentConfigLoader extends Mage_Core_Helper_Abstract
{
    protected const ENV_STARTS_WITH = 'OPENMAGE_CONFIG';
    protected const ENV_FEATURE_ENABLED = 'OPENMAGE_CONFIG_OVERRIDE_ALLOWED';
    protected const ENV_KEY_SEPARATOR = '__';
    protected const CONFIG_KEY_DEFAULT = 'DEFAULT';
    protected const CONFIG_KEY_WEBSITES = 'WEBSITES';
    protected const CONFIG_KEY_STORES = 'STORES';
    /**
     * To be used as regex condition
     */
    protected const ALLOWED_CHARS = ['A-Z', '-', '_'];
    protected $_moduleName = 'Mage_Core';
    protected array $envStore = [];

    /**
     * Load configuration values from ENV variables into xml config object
     *
     * Environment variables work on this schema:
     *
     * self::ENV_STARTS_WITH . self::ENV_KEY_SEPARATOR (OPENMAGE_CONFIG__)
     *        ^ Prefix (required)
     *                  <SCOPE>__
     *                     ^ Where scope is DEFAULT, WEBSITES__<WEBSITE_CODE> or STORES__<STORE_CODE>
     *                           <SYSTEM_VARIABLE_NAME>
     *                                   ^ Where GROUP, SECTION and FIELD are separated by self::ENV_KEY_SEPARATOR
     *
     * Each example will override the 'general/store_information/name' value.
     * Override from the default configuration:
     * @example OPENMAGE_CONFIG__DEFAULT__GENERAL__STORE_INFORMATION__NAME=default
     * Override the website 'base' configuration:
     * @example OPENMAGE_CONFIG__WEBSITES__BASE__GENERAL__STORE_INFORMATION__NAME=website
     * Override the store 'german' configuration:
     * @example OPENMAGE_CONFIG__STORES__GERMAN__GENERAL__STORE_INFORMATION__NAME=store_german
     *
     * @return void
     */
    public function overrideEnvironment(Varien_Simplexml_Config $xmlConfig)
    {
        $data = Mage::registry('current_env_config');
        if ($data) {
            return;
        }
        $env = $this->getEnv();

        foreach ($env as $configKey => $value) {
            if (!$this->isConfigKeyValid($configKey)) {
                continue;
            }

            [$configKeyParts, $scope] = $this->getConfigKey($configKey);

            switch ($scope) {
                case static::CONFIG_KEY_DEFAULT:
                    [$unused1, $unused2, $section, $group, $field] = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $nodePath = $this->buildNodePath($scope, $path);
                    $xmlConfig->setNode($nodePath, $value);
                    try {
                        foreach (['0', 'admin'] as $store) {
                            $store = Mage::app()->getStore($store);
                            $this->setCache($store, $value, $path);
                        }
                    } catch (Throwable $exception) {
                        // invalid store, intentionally empty
                    }
                    break;

                case static::CONFIG_KEY_WEBSITES:
                case static::CONFIG_KEY_STORES:
                    [$unused1, $unused2, $storeCode, $section, $group, $field] = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $storeCode = strtolower($storeCode);
                    $scope = strtolower($scope);
                    $nodePath = sprintf('%s/%s/%s', $scope, $storeCode, $path);
                    $xmlConfig->setNode($nodePath, $value);
                    try {
                        if (!str_contains($nodePath, 'websites')) {
                            foreach ([$storeCode, 'admin'] as $store) {
                                $store = Mage::app()->getStore($store);
                                $this->setCache($store, $value, $path);
                            }
                        }
                    } catch (Throwable $exception) {
                        // invalid store, intentionally empty
                    }
                    break;
            }
        }
        Mage::register('current_env_config', true, true);
    }

    public function hasPath(string $wantedPath): bool
    {
        $data = Mage::registry("config_env_has_path_$wantedPath");
        if ($data !== null) {
            return $data;
        }
        $env = $this->getEnv();
        $config = [];

        foreach ($env as $configKey => $value) {
            if (!$this->isConfigKeyValid($configKey)) {
                continue;
            }

            [$configKeyParts, $scope] = $this->getConfigKey($configKey);

            switch ($scope) {
                case static::CONFIG_KEY_DEFAULT:
                    [$unused1, $unused2, $section, $group, $field] = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $nodePath = $this->buildNodePath($scope, $path);
                    $config[$nodePath] = $value;
                    break;

                case static::CONFIG_KEY_WEBSITES:
                case static::CONFIG_KEY_STORES:
                    [$unused1, $unused2, $storeCode, $section, $group, $field] = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $storeCode = strtolower($storeCode);
                    $scope = strtolower($scope);
                    $nodePath = sprintf('%s/%s/%s', $scope, $storeCode, $path);
                    $config[$nodePath] = $value;
                    break;
            }
        }
        $hasConfig = array_key_exists($wantedPath, $config);
        Mage::register("config_env_has_path_$wantedPath", $hasConfig);
        return $hasConfig;
    }

    public function getAsArray(string $wantedStore): array
    {
        if (empty($wantedStore)) {
            $wantedStore = 'default';
        }
        $data = Mage::registry("config_env_array_$wantedStore");
        if ($data !== null) {
            return $data;
        }
        $env = $this->getEnv();
        $config = [];

        foreach ($env as $configKey => $value) {
            if (!$this->isConfigKeyValid($configKey)) {
                continue;
            }

            [$configKeyParts, $scope] = $this->getConfigKey($configKey);

            switch ($scope) {
                case static::CONFIG_KEY_DEFAULT:
                    [$unused1, $unused2, $section, $group, $field] = $configKeyParts;
                    $path = $this->buildPath($section, $group, $field);
                    $config[$path] = $value;
                    break;
                case static::CONFIG_KEY_WEBSITES:
                case static::CONFIG_KEY_STORES:
                    [$unused1, $unused2, $storeCode, $section, $group, $field] = $configKeyParts;
                    if (strtolower($storeCode) !== strtolower($wantedStore)) {
                        break;
                    }
                    $path = $this->buildPath($section, $group, $field);
                    $config[$path] = $value;
                    break;
            }
        }
        Mage::register("config_env_array_$wantedStore", $config);
        return $config;
    }

    /**
     * @internal method mostly for mocking
     */
    public function setEnvStore(array $envStorage): void
    {
        $this->envStore = $envStorage;
    }

    public function getEnv(): array
    {
        if (empty($this->envStore)) {
            $env = getenv();
            $env = array_filter($env, function ($key) {
                return str_starts_with($key, static::ENV_STARTS_WITH);
            }, ARRAY_FILTER_USE_KEY);
            $this->envStore = $env;
        }
        if (!isset($this->envStore[static::ENV_FEATURE_ENABLED]) ||
            (bool) $this->envStore[static::ENV_FEATURE_ENABLED] === false
        ) {
            $this->envStore = [];
            return $this->envStore;
        }
        return $this->envStore;
    }

    protected function setCache(Mage_Core_Model_Store $store, $value, string $path): void
    {
        $refObject = new ReflectionObject($store);
        $refProperty = $refObject->getProperty('_configCache');
        $refProperty->setAccessible(true);
        $configCache = $refProperty->getValue($store);
        if (!is_array($configCache)) {
            $configCache = [];
        }
        $configCache[$path] = $value;
        $store->setConfigCache($configCache);
    }

    protected function getConfigKey(string $configKey): array
    {
        $configKeyParts = array_filter(
            explode(
                static::ENV_KEY_SEPARATOR,
                $configKey,
            ),
            'trim',
        );
        [$unused, $scope] = $configKeyParts;
        return [$configKeyParts, $scope];
    }

    protected function isConfigKeyValid(string $configKey): bool
    {
        $sectionGroupFieldRegexp = sprintf('([%s]*)', implode('', static::ALLOWED_CHARS));
        $allowedChars = sprintf('[%s]', implode('', static::ALLOWED_CHARS));
        $regexp = '/' . static::ENV_STARTS_WITH . static::ENV_KEY_SEPARATOR . '(WEBSITES' . static::ENV_KEY_SEPARATOR
            . $allowedChars . '+|DEFAULT|STORES' . static::ENV_KEY_SEPARATOR . $allowedChars . '+)'
            . static::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp
            . static::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp
            . static::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp . '/';
        // /OPENMAGE_CONFIG__(WEBSITES__[A-Z-_]+|DEFAULT|STORES__[A-Z-_]+)__([A-Z-_]*)__([A-Z-_]*)__([A-Z-_]*)/

        return preg_match($regexp, $configKey);
    }

    /**
     * Build configuration path.
     */
    protected function buildPath(string $section, string $group, string $field): string
    {
        return strtolower(implode('/', [$section, $group, $field]));
    }

    /**
     * Build configuration node path.
     */
    protected function buildNodePath(string $scope, string $path): string
    {
        return strtolower($scope) . '/' . $path;
    }
}
