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
    public const ENV_STARTS_WITH = 'OPENMAGE_CONFIG';

    public const ENV_FEATURE_ENABLED = 'OPENMAGE_CONFIG_OVERRIDE_ALLOWED';

    public const ENV_KEY_SEPARATOR = '__';

    public const CONFIG_KEY_DEFAULT = 'DEFAULT';

    public const CONFIG_KEY_WEBSITES = 'WEBSITES';

    public const CONFIG_KEY_STORES = 'STORES';

    public const REGISTRY_KEY = 'current_env_config';

    /**
     * To be used as regex condition
     */
    protected const ALLOWED_CHARS = ['A-Z', '-', '_'];

    protected $_moduleName = 'Mage_Core';

    /**
     * @var array<string, string>
     */
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
     */
    public function overrideEnvironment(Varien_Simplexml_Config $xmlConfig): void
    {
        $data = Mage::registry(self::REGISTRY_KEY);
        if ($data) {
            return;
        }

        $env = $this->getEnv();

        foreach ($env as $configKey => $value) {
            if (!$this->isConfigKeyValid($configKey)) {
                continue;
            }

            $override   = $this->getConfigKey($configKey);
            $scope      = $override->getScope();
            $storeCode  = $override->getStoreCode();

            $path       = $this->buildPath($override->getSection(), $override->getGroup(), $override->getField());

            switch ($scope) {
                case self::CONFIG_KEY_DEFAULT:
                    try {
                        $store = Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE);
                        if ($store instanceof Mage_Core_Model_Store) {
                            $nodePath = $this->buildNodePath($scope, $path, $storeCode);
                            $xmlConfig->setNode($nodePath, $value);
                            $this->setCache($store, $value, $path);
                        }

                        $stores = Mage::app()->getStores(withDefault: true);
                        foreach ($stores as $store) {
                            $nodePath = $this->buildNodePath(self::CONFIG_KEY_STORES, $path, $store->getCode());
                            $xmlConfig->setNode($nodePath, $value);
                            $this->setCache($store, $value, $path);
                        }
                    } catch (Throwable) {
                        // invalid store, intentionally empty
                    }

                    break;

                case self::CONFIG_KEY_WEBSITES:
                    try {
                        $websites = Mage::app()->getWebsites();
                        foreach ($websites as $website) {
                            if (strtolower($website->getCode()) !== strtolower($storeCode)) {
                                continue;
                            }

                            $nodePath = $this->buildNodePath($scope, $path, $storeCode);
                            $xmlConfig->setNode($nodePath, $value);

                            $stores = $website->getStores();
                            foreach ($stores as $store) {
                                if ($store instanceof Mage_Core_Model_Store && $store->getId()) {
                                    $nodePath = $this->buildNodePath(self::CONFIG_KEY_STORES, $path, $store->getCode());
                                    $xmlConfig->setNode($nodePath, $value);
                                    $this->setCache($store, $value, $path);
                                }
                            }
                        }
                    } catch (Throwable) {
                        // invalid store, intentionally empty
                    }

                    break;

                case self::CONFIG_KEY_STORES:
                    try {
                        $stores = Mage::app()->getStores();
                        foreach ($stores as $store) {
                            if (strtolower($store->getCode()) !== strtolower($storeCode)) {
                                continue;
                            }

                            $nodePath = $this->buildNodePath($scope, $path, $store->getCode());
                            $xmlConfig->setNode($nodePath, $value);
                            $this->setCache($store, $value, $path);
                        }
                    } catch (Throwable) {
                        // invalid store, intentionally empty
                    }

                    break;
            }
        }

        try {
            Mage::register(self::REGISTRY_KEY, true, true);
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::logException($mageCoreException);
        }
    }

    public function hasPath(string $wantedPath): bool
    {
        /** @var null|bool $data */
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

            $override   = $this->getConfigKey($configKey);
            $scope      = $override->getScope();
            $path       = $this->buildPath($override->getSection(), $override->getGroup(), $override->getField());

            switch ($scope) {
                case self::CONFIG_KEY_DEFAULT:
                    $nodePath = $this->buildNodePath($scope, $path);
                    $config[$nodePath] = $value;

                    try {
                        $websites = Mage::app()->getWebsites();
                        foreach ($websites as $website) {
                            $nodePath = $this->buildNodePath(self::CONFIG_KEY_WEBSITES, $path, $website->getCode());
                            $config[$nodePath] = $value;
                        }

                        $stores = Mage::app()->getStores(withDefault: true);
                        foreach ($stores as $store) {
                            $nodePath = $this->buildNodePath(self::CONFIG_KEY_STORES, $path, $store->getCode());
                            $config[$nodePath] = $value;
                        }
                    } catch (Throwable) {
                        // invalid store, intentionally empty
                    }

                    break;

                case self::CONFIG_KEY_WEBSITES:
                    try {
                        $websites = Mage::app()->getWebsites();
                        foreach ($websites as $website) {
                            if (strtolower($website->getCode()) !== strtolower($override->getStoreCode())) {
                                continue;
                            }

                            $nodePath = $this->buildNodePath($scope, $path, $website->getCode());
                            $config[$nodePath] = $value;

                            $stores = $website->getStores();
                            foreach ($stores as $store) {
                                if ($store instanceof Mage_Core_Model_Store && $store->getId()) {
                                    $nodePath = $this->buildNodePath(self::CONFIG_KEY_STORES, $path, $store->getCode());
                                    $config[$nodePath] = $value;
                                }
                            }
                        }
                    } catch (Throwable) {
                        // invalid store, intentionally empty
                    }

                    break;
                case self::CONFIG_KEY_STORES:
                    $nodePath = $this->buildNodePath($scope, $path, $override->getStoreCode());
                    $config[$nodePath] = $value;

                    break;
            }
        }

        $hasConfig = array_key_exists($wantedPath, $config);

        try {
            Mage::register("config_env_has_path_$wantedPath", $hasConfig);
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::logException($mageCoreException);
        }

        return $hasConfig;
    }

    /**
     * @return array<string, string>
     */
    public function getAsArray(string $wantedStore): array
    {
        if (empty($wantedStore)) {
            $wantedStore = 'default';
        }

        /** @var null|array<string, string> $data */
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

            $override   = $this->getConfigKey($configKey);
            $path       = $this->buildPath($override->getSection(), $override->getGroup(), $override->getField());

            switch ($override->getScope()) {
                case self::CONFIG_KEY_DEFAULT:
                    $config[$path] = $value;
                    break;
                case self::CONFIG_KEY_WEBSITES:
                case self::CONFIG_KEY_STORES:
                    if (strtolower($override->getStoreCode()) !== strtolower($wantedStore)) {
                        break;
                    }

                    $config[$path] = $value;
                    break;
            }
        }

        try {
            Mage::register("config_env_array_$wantedStore", $config);
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::logException($mageCoreException);
        }

        return $config;
    }

    /**
     * @param array<string, int|string> $envStorage
     * @internal method mostly for mocking
     */
    public function setEnvStore(array $envStorage): void
    {
        $this->envStore = $envStorage;
    }

    /**
     * @return array<string, int|string>
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function getEnv(): array
    {
        if (empty($this->envStore)) {
            // Use $_ENV instead of getenv() because phpdotenv populates $_ENV with both system environment variables
            // and variables from the .env file. This ensures that configuration overrides from .env are respected.
            // getenv() would only return system environment variables, not those loaded from .env.
            $env = array_filter($_ENV, function ($key) {
                return str_starts_with($key, self::ENV_STARTS_WITH);
            }, ARRAY_FILTER_USE_KEY);
            $this->envStore = $env;
        }

        if (!isset($this->envStore[self::ENV_FEATURE_ENABLED])
            || (bool) $this->envStore[self::ENV_FEATURE_ENABLED] === false
        ) {
            $this->envStore = [];
            return $this->envStore;
        }

        return $this->envStore;
    }

    protected function setCache(Mage_Core_Model_Store $store, string $value, string $path): void
    {
        $refObject = new ReflectionObject($store);
        $refProperty = $refObject->getProperty('_configCache');

        $configCache = $refProperty->getValue($store);
        if (!is_array($configCache)) {
            $configCache = [];
        }

        $configCache[$path] = $value;
        $store->setConfigCache($configCache);
    }

    protected function getConfigKey(string $configKey): Mage_Core_Helper_EnvironmentConfigLoader_Override
    {
        $configKeyParts = array_filter(
            explode(
                self::ENV_KEY_SEPARATOR,
                $configKey,
            ),
            trim(...),
        );

        $scope      = $configKeyParts[1];
        $isDefault  = $scope === self::CONFIG_KEY_DEFAULT;
        $storeCode  = $isDefault ? '' : $configKeyParts[2];
        $section    = $isDefault ? $configKeyParts[2] : $configKeyParts[3];
        $group      = $isDefault ? $configKeyParts[3] : $configKeyParts[4];
        $field      = $isDefault ? $configKeyParts[4] : $configKeyParts[5];

        return new Mage_Core_Helper_EnvironmentConfigLoader_Override(
            scope: $scope,
            section: $section,
            group: $group,
            field: $field,
            storeCode: $storeCode,
        );
    }

    protected function isConfigKeyValid(string $configKey): bool
    {
        $sectionGroupFieldRegexp = sprintf('([%s]*)', implode('', self::ALLOWED_CHARS));
        $allowedChars = sprintf('[%s]', implode('', self::ALLOWED_CHARS));
        $regexp = '/' . self::ENV_STARTS_WITH . self::ENV_KEY_SEPARATOR . '(WEBSITES' . self::ENV_KEY_SEPARATOR
            . '[A-Z][A-Z0-9]' . '+|DEFAULT|STORES' . self::ENV_KEY_SEPARATOR . $allowedChars . '+)'
            . self::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp
            . self::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp
            . self::ENV_KEY_SEPARATOR . $sectionGroupFieldRegexp . '/';
        // /OPENMAGE_CONFIG__(WEBSITES__[A-Z][A-Z0-9]+|DEFAULT|STORES__[A-Z-_]+)__([A-Z-_]*)__([A-Z-_]*)__([A-Z-_]*)/

        return (bool) preg_match($regexp, $configKey);
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
    protected function buildNodePath(string $scope, string $path, string $storeCode = ''): string
    {
        return strtolower($scope) . ($storeCode ? '/' . strtolower($storeCode) : '') . '/' . $path;
    }
}
