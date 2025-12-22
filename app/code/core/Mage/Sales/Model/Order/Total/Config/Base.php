<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Configuration class for totals
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Total_Config_Base extends Mage_Sales_Model_Config_Ordered
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_collectors';

    /**
     * Total models list
     *
     * @var array
     */
    protected $_totalModels = [];

    /**
     * Configuration path where to collect registered totals
     *
     * @var string
     */
    protected $_totalsConfigNode = 'totals';

    /**
     * Init model class by configuration
     *
     * @param  string                                $class
     * @param  string                                $totalCode
     * @param  Mage_Core_Model_Config_Element        $totalConfig
     * @return Mage_Sales_Model_Order_Total_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _initModelInstance($class, $totalCode, $totalConfig)
    {
        $model = Mage::getModel($class);
        if (!$model instanceof Mage_Sales_Model_Order_Total_Abstract) {
            Mage::throwException(Mage::helper('sales')->__('Total model should be extended from Mage_Sales_Model_Order_Total_Abstract.'));
        }

        $model->setCode($totalCode);
        $model->setTotalConfigNode($totalConfig);
        $this->_modelsConfig[$totalCode] = $this->_prepareConfigArray($totalCode, $totalConfig);
        $this->_modelsConfig[$totalCode] = $model->processConfigArray($this->_modelsConfig[$totalCode]);
        return $model;
    }

    /**
     * Retrieve total calculation models
     *
     * @return array
     */
    public function getTotalModels()
    {
        if (empty($this->_totalModels)) {
            $this->_initModels();
            $this->_initCollectors();
            $this->_totalModels = $this->_collectors;
        }

        return $this->_totalModels;
    }
}
