<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Address Total Collector model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Quote_Address_Total_Collector extends Mage_Sales_Model_Config_Ordered
{
    /**
     * Path to sort order values of checkout totals
     */
    public const XML_PATH_SALES_TOTALS_SORT = 'sales/totals_sort';

    /**
     * Total models array ordered for right display sequence
     *
     * @var array
     */
    protected $_retrievers = [];

    /**
     * Corresponding store object
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Configuration path where to collect registered totals
     *
     * @var string
     */
    protected $_totalsConfigNode = 'global/sales/quote/totals';

    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_quote_collectors';

    /**
     * Init corresponding total models
     *
     * @param array $options
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function __construct($options)
    {
        if (isset($options['store'])) {
            $this->_store = $options['store'];
        } else {
            $this->_store = Mage::app()->getStore();
        }

        $this->_initModels()
            ->_initCollectors()
            ->_initRetrievers();
    }

    /**
     * Get total models array ordered for right calculation logic
     *
     * @return array
     */
    public function getCollectors()
    {
        return $this->_collectors;
    }

    /**
     * Get total models array ordered for right display sequence
     *
     * @return array
     */
    public function getRetrievers()
    {
        return $this->_retrievers;
    }

    /**
     * Init model class by configuration
     *
     * @param string $class
     * @param string $totalCode
     * @param Mage_Core_Model_Config_Element $totalConfig
     * @return false|Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _initModelInstance($class, $totalCode, $totalConfig)
    {
        $model = Mage::getModel($class);
        if (!$model instanceof Mage_Sales_Model_Quote_Address_Total_Abstract) {
            Mage::throwException(
                Mage::helper('sales')->__('The address total model should be extended from Mage_Sales_Model_Quote_Address_Total_Abstract.'),
            );
        }

        $model->setCode($totalCode);
        $this->_modelsConfig[$totalCode] = $this->_prepareConfigArray($totalCode, $totalConfig);
        $this->_modelsConfig[$totalCode] = $model->processConfigArray(
            $this->_modelsConfig[$totalCode],
            $this->_store,
        );

        return $model;
    }

    /**
     * Initialize total models configuration and objects
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _initModels()
    {
        $totalsConfig = Mage::getConfig()->getNode($this->_totalsConfigNode);

        foreach ($totalsConfig->children() as $totalCode => $totalConfig) {
            $class = $totalConfig->getClassName();
            if (!empty($class)) {
                $this->_models[$totalCode] = $this->_initModelInstance($class, $totalCode, $totalConfig);
            }
        }

        return $this;
    }

    /**
     * Initialize retrievers array
     *
     * @return $this
     */
    protected function _initRetrievers()
    {
        $sorts = Mage::getStoreConfig(self::XML_PATH_SALES_TOTALS_SORT, $this->_store);
        foreach ($sorts as $code => $sortOrder) {
            if (isset($this->_models[$code])) {
                // Reserve enough space for collisions
                $retrieverId = 100 * (int) $sortOrder;
                // Check if there is a retriever with such id and find next available position if needed
                while (isset($this->_retrievers[$retrieverId])) {
                    $retrieverId++;
                }

                $this->_retrievers[$retrieverId] = $this->_models[$code];
            }
        }

        ksort($this->_retrievers);
        $notSorted = array_diff(array_keys($this->_models), array_keys($sorts));
        foreach ($notSorted as $code) {
            $this->_retrievers[] = $this->_models[$code];
        }

        return $this;
    }
}
