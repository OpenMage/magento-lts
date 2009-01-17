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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Payment Action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Source_StandardAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Paypal_Model_Standard::PAYMENT_TYPE_AUTH, 'label' => Mage::helper('paypal')->__('Authorization')),
            array('value' => Mage_Paypal_Model_Standard::PAYMENT_TYPE_SALE, 'label' => Mage::helper('paypal')->__('Sale')),
        );
    }
}