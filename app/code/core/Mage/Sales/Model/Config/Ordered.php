<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Configuration class for ordered items
 *
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Model_Config_Ordered extends Mage_Core_Model_Config_Base
{
    /**
     * Cache key for collectors
     *
     * @var string|null
     */
    protected $_collectorsCacheKey = null;

    /**
     * Configuration path where to collect registered totals
     *
     * @var string|null
     */
    protected $_totalsConfigNode = null;

    /**
     * Prepared models
     *
     * @var array
     */
    protected $_models = [];

    /**
     * Models configuration
     *
     * @var array
     */
    protected $_modelsConfig = [];

    /**
     * Sorted models
     *
     * @var array
     */
    protected $_collectors = [];

    /**
     * Initialize total models configuration and objects
     *
     * @return $this
     */
    protected function _initModels()
    {
        $totalsConfig = $this->getNode($this->_totalsConfigNode);

        foreach ($totalsConfig->children() as $totalCode => $totalConfig) {
            $class = $totalConfig->getClassName();
            if (!empty($class)) {
                $this->_models[$totalCode] = $this->_initModelInstance($class, $totalCode, $totalConfig);
            }
        }
        return $this;
    }

    /**
     * Init model class by configuration
     *
     * @abstract
     * @param string $class
     * @param string $totalCode
     * @param array $totalConfig
     * @return mixed
     */
    abstract protected function _initModelInstance($class, $totalCode, $totalConfig);

    /**
     * Prepare configuration array for total model
     *
     * @param   string $code
     * @param   Mage_Core_Model_Config_Element $totalConfig
     * @return  array
     */
    protected function _prepareConfigArray($code, $totalConfig)
    {
        $totalConfig = (array) $totalConfig;
        if (isset($totalConfig['before'])) {
            $totalConfig['before'] = explode(',', $totalConfig['before']);
        } else {
            $totalConfig['before'] = [];
        }
        if (isset($totalConfig['after'])) {
            $totalConfig['after'] = explode(',', $totalConfig['after']);
        } else {
            $totalConfig['after'] = [];
        }
        $totalConfig['_code'] = $code;
        return $totalConfig;
    }

    /**
     * Aggregate before/after information from all items and sort totals based on this data
     *
     * @return array
     */
    protected function _getSortedCollectorCodes()
    {
        if (Mage::app()->useCache('config')) {
            $cachedData = Mage::app()->loadCache($this->_collectorsCacheKey);
            if ($cachedData) {
                return unserialize($cachedData, ['allowed_classes' => false]);
            }
        }
        $configArray = $this->_modelsConfig;
        // invoke simple sorting if the first element contains the "sort_order" key
        reset($configArray);
        $element = current($configArray);
        if (isset($element['sort_order'])) {
            uasort($configArray, [$this, '_compareSortOrder']);
        } else {
            foreach ($configArray as $code => $data) {
                foreach ($data['before'] as $beforeCode) {
                    if (!isset($configArray[$beforeCode])) {
                        continue;
                    }
                    $configArray[$code]['before'] = array_unique(array_merge(
                        $configArray[$code]['before'],
                        $configArray[$beforeCode]['before'],
                    ));
                    $configArray[$beforeCode]['after'] = array_merge(
                        $configArray[$beforeCode]['after'],
                        [$code],
                        $data['after'],
                    );
                    $configArray[$beforeCode]['after'] = array_unique($configArray[$beforeCode]['after']);
                }
                foreach ($data['after'] as $afterCode) {
                    if (!isset($configArray[$afterCode])) {
                        continue;
                    }
                    $configArray[$code]['after'] = array_unique(array_merge(
                        $configArray[$code]['after'],
                        $configArray[$afterCode]['after'],
                    ));
                    $configArray[$afterCode]['before'] = array_merge(
                        $configArray[$afterCode]['before'],
                        [$code],
                        $data['before'],
                    );
                    $configArray[$afterCode]['before'] = array_unique($configArray[$afterCode]['before']);
                }
            }
            uasort($configArray, [$this, '_compareTotals']);
        }
        $sortedCollectors = array_keys($configArray);
        if (Mage::app()->useCache('config')) {
            Mage::app()->saveCache(serialize($sortedCollectors), $this->_collectorsCacheKey, [
                Mage_Core_Model_Config::CACHE_TAG,
            ]);
        }
        return $sortedCollectors;
    }

    /**
     * Initialize collectors array.
     * Collectors array is array of total models ordered based on configuration settings
     *
     * @return  $this
     */
    protected function _initCollectors()
    {
        $sortedCodes = $this->_getSortedCollectorCodes();
        foreach ($sortedCodes as $code) {
            $this->_collectors[$code] = $this->_models[$code];
        }

        return $this;
    }

    /**
     * Callback that uses after/before for comparison
     *
     * @param   array $a
     * @param   array $b
     * @return  int
     */
    protected function _compareTotals($a, $b)
    {
        $aCode = $a['_code'];
        $bCode = $b['_code'];
        if (in_array($aCode, $b['after']) || in_array($bCode, $a['before'])) {
            $res = -1;
        } elseif (in_array($bCode, $a['after']) || in_array($aCode, $b['before'])) {
            $res = 1;
        } else {
            $res = 0;
        }
        return $res;
    }

    /**
     * Callback that uses sort_order for comparison
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _compareSortOrder($a, $b)
    {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }
        if ($a['sort_order'] > $b['sort_order']) {
            $res = 1;
        } elseif ($a['sort_order'] < $b['sort_order']) {
            $res = -1;
        } else {
            $res = 0;
        }
        return $res;
    }
}
