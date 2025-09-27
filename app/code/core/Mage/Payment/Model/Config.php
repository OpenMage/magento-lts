<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * Payment configuration model
 *
 * Used for retrieving configuration data by payment models
 *
 * @package    Mage_Payment
 */
class Mage_Payment_Model_Config
{
    protected static $_methods;

    /**
     * Retrieve active system payments
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return array
     */
    public function getActiveMethods($store = null)
    {
        $methods = [];
        $config = Mage::getStoreConfig('payment', $store);
        foreach ($config as $code => $methodConfig) {
            if (Mage::getStoreConfigFlag('payment/' . $code . '/active', $store)) {
                if (array_key_exists('model', $methodConfig)) {
                    $methodModel = Mage::getModel($methodConfig['model']);
                    if ($methodModel && $methodModel->getConfigData('active', $store)) {
                        $methods[$code] = $this->_getMethod($code, $methodConfig);
                    }
                }
            }
        }
        return $methods;
    }

    /**
     * Retrieve all system payments
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return array
     */
    public function getAllMethods($store = null)
    {
        $methods = [];
        $config = Mage::getStoreConfig('payment', $store);
        foreach ($config as $code => $methodConfig) {
            $data = $this->_getMethod($code, $methodConfig);
            if ($data !== false) {
                $methods[$code] = $data;
            }
        }
        return $methods;
    }

    /**
     * @param string $code
     * @param array $config
     * @param null|string|bool|int|Mage_Core_Model_Store $store $store
     * @return false|Mage_Payment_Model_Method_Abstract
     */
    protected function _getMethod($code, $config, $store = null)
    {
        if (isset(self::$_methods[$code])) {
            return self::$_methods[$code];
        }
        if (empty($config['model'])) {
            return false;
        }
        $modelName = $config['model'];
        $method = Mage::getModel($modelName);
        if (!$method) {
            return false;
        }
        $method->setId($code)->setStore($store);
        self::$_methods[$code] = $method;
        return self::$_methods[$code];
    }

    /**
     * Retrieve array of credit card types
     *
     * @return array
     */
    public function getCcTypes()
    {
        $_types = Mage::getConfig()->getNode('global/payment/cc/types')->asArray();

        uasort($_types, ['Mage_Payment_Model_Config', 'compareCcTypes']);

        $types = [];
        foreach ($_types as $data) {
            if (isset($data['code']) && isset($data['name'])) {
                $types[$data['code']] = $data['name'];
            }
        }
        return $types;
    }

    /**
     * Retrieve list of months translation
     *
     * @return array
     */
    public function getMonths()
    {
        $data = Mage::app()->getLocale()->getTranslationList('month');
        foreach ($data as $key => $value) {
            $monthNum = ($key < 10) ? '0' . $key : $key;
            $data[$key] = $monthNum . ' - ' . $value;
        }
        return $data;
    }

    /**
     * Retrieve array of available years
     *
     * @return array
     */
    public function getYears()
    {
        $years = [];
        $first = date('Y');

        for ($index = 0; $index <= 10; $index++) {
            $year = $first + $index;
            $years[$year] = $year;
        }
        return $years;
    }

    /**
     * Statis Method for compare sort order of CC Types
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function compareCcTypes($a, $b)
    {
        if (!isset($a['order'])) {
            $a['order'] = 0;
        }

        if (!isset($b['order'])) {
            $b['order'] = 0;
        }

        if ($a['order'] == $b['order']) {
            return 0;
        } elseif ($a['order'] > $b['order']) {
            return 1;
        } else {
            return -1;
        }
    }
}
