<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order shipping address backend
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Attribute_Backend_Shipping extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $shippingAddressId = $object->getShippingAddressId();
        if (is_null($shippingAddressId)) {
            $object->unsetShippingAddressId();
        }

        return $this;
    }

    /**
     * @param Varien_Object $object
     * @return $this
     * @throws Exception
     */
    public function afterSave($object)
    {
        $shippingAddressId = false;
        foreach ($object->getAddressesCollection() as $address) {
            if ($address->getAddressType() == 'shipping') {
                $shippingAddressId = $address->getId();
            }
        }

        if ($shippingAddressId) {
            $object->setShippingAddressId($shippingAddressId);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getAttributeCode());
        }

        return $this;
    }
}
