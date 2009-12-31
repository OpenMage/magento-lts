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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address entity resource model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Entity_Address extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('customer_address')->setConnection(
            $resource->getConnection('customer_read'),
            $resource->getConnection('customer_write')
        );
    }

    protected function _afterSave(Varien_Object $address)
    {
        if ($address->getId() && ($address->getIsDefaultBilling() || $address->getIsDefaultShipping())) {
            $customer = Mage::getModel('customer/customer')
                ->load($address->getCustomerId());

            if ($address->getIsDefaultBilling()) {
                $customer->setDefaultBilling($address->getId());
            }
            if ($address->getIsDefaultShipping()) {
                $customer->setDefaultShipping($address->getId());
            }
            $customer->save();
        }
        return $this;
    }

    /**
     * Return customer id
     *
     * @deprecated
     * @param Mage_Customer_Model_Address $object
     * @return integer
     */
    public function getCustomerId($object)
    {
        return $object->getData('customer_id') ? $object->getData('customer_id') :$object->getParentId();
    }

    /**
     * Set customer id
     *
     * @deprecated
     * @param Mage_Customer_Model_Address $object
     * @param integer $id
     * @return Mage_Customer_Model_Address
     */
    public function setCustomerId($object, $id)
    {
        $object->setParentId($id);
        $object->setData('customer_id', $id);
        return $object;
    }
}