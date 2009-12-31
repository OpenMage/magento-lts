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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote payment mysql4 resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Mysql4_Quote_Payment extends Mage_Sales_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/quote_payment', 'payment_id');
    }

    /**
     * Also serialize additional information
     *
     * @param Mage_Core_Model_Abstract $payment
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $payment)
    {
        $additionalInformation = $payment->getData('additional_information');        
        if (empty($additionalInformation)) {
            $payment->setData('additional_information', null);
        } elseif (is_array($additionalInformation)) {
            $payment->setData('additional_information', serialize($additionalInformation));
        }
        return parent::_beforeSave($payment);
    }

    /**
     * Unserialize additional information after loading the object
     *
     * @param Mage_Core_Model_Abstract $object $payment
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $payment)
    {
        $this->unserializeFields($payment);
        return parent::_afterLoad($payment);
    }

    /**
     * Unserialize additional information after saving the object
     *
     * @param Mage_Core_Model_Abstract $payment
     */
    protected function _afterSave(Mage_Core_Model_Abstract $payment)
    {
        $this->unserializeFields($payment);
        return parent::_afterSave($payment);
    }

    /**
     * Unserialize additional data if required
     * @param Mage_Sales_Model_Quote_Payment $payment
     */
    public function unserializeFields(Mage_Sales_Model_Quote_Payment $payment)
    {
        $additionalInformation = $payment->getData('additional_information');
        if (empty($additionalInformation)) {
            $payment->setData('additional_information', array());
        } elseif (!is_array($additionalInformation)) {
            $payment->setData('additional_information', unserialize($additionalInformation));
        }
    }
    
}
