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
 *
 * @phpstan-import-type ConfigStoreId from Mage
 */
class Mage_Payment_Model_Config
{
    /**
     * @var array<string, Mage_Payment_Model_Method_Abstract>
     */
    protected static $_methods;

    /**
     * Retrieve active system payments
     *
     * @param  ConfigStoreId                                     $store
     * @return array<string, Mage_Payment_Model_Method_Abstract>
     */
    public function getActiveMethods($store = null)
    {
        $methods = [];
        $config = Mage::getStoreConfig('payment', $store);
        foreach ($config as $code => $methodConfig) {
            if (Mage::getStoreConfigFlag('payment/' . $code . '/active', $store) && array_key_exists('model', $methodConfig)) {
                $methodModel = $this->_getMethod($code, $methodConfig);
                if ($methodModel && $methodModel->getConfigData('active', $store)) {
                    $methods[$code] = $methodModel;
                }
            }
        }

        return $methods;
    }

    /**
     * Retrieve all system payments
     *
     * @param  ConfigStoreId                                     $store
     * @return array<string, Mage_Payment_Model_Method_Abstract>
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
     * @param  string                                   $code
     * @param  array                                    $config
     * @param  ConfigStoreId                            $store
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

        /** @var Mage_Payment_Model_Method_Abstract $method */
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
     * @return array<string, string>
     */
    public function getCcTypes()
    {
        $_types = Mage::getConfig()?->getNode('global/payment/cc/types');

        if (!$_types instanceof Varien_Simplexml_Element) {
            return [];
        }

        $_types = $_types->asArray();

        if (!is_array($_types)) {
            return [];
        }

        uasort($_types, self::compareCcTypes(...));

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
     * @return array<int, string>
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
     * @return array<int, int>
     */
    public function getYears()
    {
        $years = [];
        $first = (int) Mage::helper('core/clock')->format('Y');

        for ($index = 0; $index <= 10; $index++) {
            $year = $first + $index;
            $years[$year] = $year;
        }

        return $years;
    }

    /**
     * Static Method for compare sort order of CC Types
     *
     * @param  array $sortA
     * @param  array $sortB
     * @return int
     */
    public static function compareCcTypes($sortA, $sortB)
    {
        return ($sortA['order'] ?? 0) <=> ($sortB['order'] ?? 0);
    }
}
