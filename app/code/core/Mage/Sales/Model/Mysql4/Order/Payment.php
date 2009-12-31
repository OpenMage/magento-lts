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
 * Order payment entity resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Mysql4_Order_Payment extends Mage_Eav_Model_Entity_Abstract
{

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('order_payment')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write')
        );
    }

    /**
     * Also serialize additional information
     *
     * @param Varien_Object $payment
     */
    protected function _beforeSave(Varien_Object $payment)
    {
        $additionalInformation = $payment->getData('additional_information');
        if (empty($additionalInformation)) {
            $payment->setData('additional_information', null);
        } elseif (is_array($additionalInformation)) {
            $payment->setData('additional_information', serialize($additionalInformation));
        }
        parent::_beforeSave($payment);
    }

    /**
     * Unserialize additional information after loading the object
     *
     * @param Varien_Object $payment
     */
    protected function _afterLoad(Varien_Object $payment)
    {
        $this->unserializeFields($payment);
        parent::_afterLoad($payment);
    }

    /**
     * Unserialize additional information after saving the object
     *
     * @param Varien_Object $payment
     */
    protected function _afterSave(Varien_Object $payment)
    {
        $this->unserializeFields($payment);
        return parent::_afterSave($payment);
    }

    /**
     * Unserialize additional data if required
     * @param Mage_Sales_Model_Order_Payment $payment
     */
    public function unserializeFields(Mage_Sales_Model_Order_Payment $payment)
    {
        $additionalInformation = $payment->getData('additional_information');
        if (empty($additionalInformation)) {
            $payment->setData('additional_information', array());
        } elseif (!is_array($additionalInformation)) {
            $payment->setData('additional_information', unserialize($additionalInformation));
        }
    }

}
