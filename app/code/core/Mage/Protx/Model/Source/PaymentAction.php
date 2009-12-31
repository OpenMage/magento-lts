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
 * @category    Mage
 * @package     Mage_Protx
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Protx Payment Actions Resource
 *
 * @category   Mage
 * @package    Mage_Protx
 * @name       Mage_Protx_Model_Source_PaymentAction
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Protx_Model_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Protx_Model_Config::PAYMENT_TYPE_PAYMENT, 'label' => Mage::helper('protx')->__('PAYMENT')),
            array('value' => Mage_Protx_Model_Config::PAYMENT_TYPE_DEFERRED, 'label' => Mage::helper('protx')->__('DEFERRED')),
            array('value' => Mage_Protx_Model_Config::PAYMENT_TYPE_AUTHENTICATE, 'label' => Mage::helper('protx')->__('AUTHENTICATE')),
        );
    }
}
