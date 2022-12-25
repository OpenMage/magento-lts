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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml creditmemo bar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Totalbar extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    protected $_totals = [];

    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block.'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());
        $this->setSource($this->getParentBlock()->getSource());
        $this->setCurrency($this->getParentBlock()->getOrder()->getOrderCurrency());

        foreach ($this->getParentBlock()->getOrderTotalbarData() as $v) {
            $this->addTotal($v[0], $v[1], $v[2]);
        }

        return parent::_beforeToHtml();
    }

    protected function getTotals()
    {
        return $this->_totals;
    }

    public function addTotal($label, $value, $grand = false)
    {
        $this->_totals[] = [
            'label' => $label,
            'value' => $value,
            'grand' => $grand
        ];
        return $this;
    }
}
