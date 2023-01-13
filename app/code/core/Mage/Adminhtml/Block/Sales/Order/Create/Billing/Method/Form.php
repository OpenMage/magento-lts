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
 * Adminhtml sales order create payment method form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form extends Mage_Payment_Block_Form_Container
{
    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract|null $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method && $method->canUseInternal() && parent::_canUseMethod($method);
    }

    /**
     * Check existing of payment methods
     *
     * @return bool
     */
    public function hasMethods()
    {
        $methods = $this->getMethods();
        if (is_array($methods) && count($methods)) {
            return true;
        }
        return false;
    }

    /**
     * Get current payment method code or the only available, if there is only one method
     *
     * @return string|false
     */
    public function getSelectedMethodCode()
    {
        // One available method. Return this method as selected, because no other variant is possible.
        $methods = $this->getMethods();
        if (count($methods) == 1) {
            foreach ($methods as $method) {
                return $method->getCode();
            }
        }

        // Several methods. If user has selected some method - then return it.
        $currentMethodCode = $this->getQuote()->getPayment()->getMethod();
        if ($currentMethodCode) {
            return $currentMethodCode;
        }

        // Several methods, but no preference for one of them.
        return false;
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('adminhtml/session_quote')->getQuote();
    }

    /*
    * Whether switch/solo card type available
    */
    public function hasSsCardType()
    {
        $availableTypes = explode(',', $this->getQuote()->getPayment()->getMethod()->getConfigData('cctypes'));
        $ssPresenations = array_intersect(['SS', 'SM', 'SO'], $availableTypes);
        if ($availableTypes && count($ssPresenations) > 0) {
            return true;
        }
        return false;
    }
}
