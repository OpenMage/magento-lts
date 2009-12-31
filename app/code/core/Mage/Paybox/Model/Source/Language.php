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
 * @package     Mage_Paybox
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * Paybox Language of the payment page Dropdown source
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paybox_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
//            array('value' => Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_AUTH, 'label' => Mage::helper('paypal')->__('Authorization')),
            array('value' => 'FRA', 'label' => Mage::helper('paybox')->__('FRA (French)')),
            array('value' => 'GBR', 'label' => Mage::helper('paybox')->__('GBR (English)')),
            array('value' => 'ESP', 'label' => Mage::helper('paybox')->__('ESP (Spanish)')),
            array('value' => 'ITA', 'label' => Mage::helper('paybox')->__('ITA (Italian)')),
            array('value' => 'DEU', 'label' => Mage::helper('paybox')->__('DEU (German)')),
            array('value' => 'NLD', 'label' => Mage::helper('paybox')->__('NLD (Dutch)')),
            array('value' => 'SWE', 'label' => Mage::helper('paybox')->__('SWE (Swedish)')),
        );
    }
}
