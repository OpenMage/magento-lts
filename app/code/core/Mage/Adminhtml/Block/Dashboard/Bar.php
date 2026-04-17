<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard bar block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Bar extends Mage_Adminhtml_Block_Dashboard_Abstract
{
    protected $_totals = [];

    protected $_currentCurrencyCode = null;

    /**
     * @var Mage_Directory_Model_Currency
     */
    protected $_currency;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dashboard/bar.phtml');
    }

    protected function getTotals()
    {
        return $this->_totals;
    }

    public function addTotal($label, $value, $isQuantity = false)
    {
        if (!$isQuantity) {
            $value = $this->format($value);
        }

        $decimals = '';
        $this->_totals[] = [
            'label' => $label,
            'value' => $value,
            'decimals' => $decimals,
        ];

        return $this;
    }

    /**
     * Formatting value specific for this store
     *
     * @param  float  $price
     * @return string
     */
    public function format($price)
    {
        return $this->getCurrency()->format($price);
    }

    /**
     * Setting currency model
     *
     * @param  Mage_Directory_Model_Currency $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

    /**
     * Retrieve currency model if not set then return currency model for current store
     *
     * @return Mage_Directory_Model_Currency
     * @throws Exception
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrency()
    {
        if (is_null($this->_currentCurrencyCode)) {
            $request = $this->getRequest();
            if ($request->getParam('store')) {
                $this->_currentCurrencyCode = Mage::app()->getStore($request->getParam('store'))->getBaseCurrency();
            } elseif ($request->getParam('website')) {
                $this->_currentCurrencyCode = Mage::app()->getWebsite($request->getParam('website'))->getBaseCurrency();
            } elseif ($request->getParam('group')) {
                $this->_currentCurrencyCode = Mage::app()->getGroup($request->getParam('group'))->getWebsite()->getBaseCurrency();
            } else {
                $this->_currentCurrencyCode = Mage::app()->getStore()->getBaseCurrency();
            }
        }

        return $this->_currentCurrencyCode;
    }
}
