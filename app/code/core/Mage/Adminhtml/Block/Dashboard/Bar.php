<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard bar block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Dashboard_Bar extends Mage_Adminhtml_Block_Dashboard_Abstract
{
    protected $_totals = [];
    protected $_currentCurrencyCode = null;

    /**
     * @var Mage_Directory_Model_Currency
     */
    protected $_currency;

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
     * @param float $price
     * @return string
     */
    public function format($price)
    {
        return $this->getCurrency()->format($price);
    }

    /**
     * Setting currency model
     *
     * @param Mage_Directory_Model_Currency $currency
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }

    /**
     * Retrieve currency model if not set then return currency model for current store
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrency()
    {
        if (is_null($this->_currentCurrencyCode)) {
            if ($this->getRequest()->getParam('store')) {
                $this->_currentCurrencyCode = Mage::app()->getStore($this->getRequest()->getParam('store'))->getBaseCurrency();
            } elseif ($this->getRequest()->getParam('website')) {
                $this->_currentCurrencyCode = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getBaseCurrency();
            } elseif ($this->getRequest()->getParam('group')) {
                $this->_currentCurrencyCode = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getWebsite()->getBaseCurrency();
            } else {
                $this->_currentCurrencyCode = Mage::app()->getStore()->getBaseCurrency();
            }
        }

        return $this->_currentCurrencyCode;
    }
}
