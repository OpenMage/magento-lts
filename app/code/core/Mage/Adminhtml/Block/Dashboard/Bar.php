<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard bar block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Dashboard_Bar extends Mage_Adminhtml_Block_Dashboard_Abstract
{
    protected $_totals = array();
    protected $_currency;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dashboard/bar.phtml');

        $this->_currency = Mage::app()->getStore($this->getParam('store'))
            ->getBaseCurrency();

    }

    protected function getTotals()
    {
        return $this->_totals;
    }

    public function addTotal($label, $value, $isQuantity=false)
    {
        /*if (!$isQuantity) {
            $value = $this->format($value);
            $decimals = substr($value, -2);
            $value = substr($value, 0, -2);
        } else {
            $value = ($value != '')?$value:0;
            $decimals = '';
        }*/
        if (!$isQuantity) {
            $value = $this->format($value);
        }
        $decimals = '';
        $this->_totals[] = array(
            'label' => $label,
            'value' => $value,
            'decimals' => $decimals,
        );

        return $this;
    }

    public function format($price)
    {
        return $this->_currency->format($price);
    }
}
