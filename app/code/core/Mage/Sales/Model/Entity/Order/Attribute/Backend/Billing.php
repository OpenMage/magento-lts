<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order billing address backend
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Entity_Order_Attribute_Backend_Billing extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Before save order billing address process
     *
     * @param  Mage_Sales_Model_Order $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $billingAddressId = $object->getBillingAddressId();
        if (is_null($billingAddressId)) {
            $object->unsetBillingAddressId();
        }

        return $this;
    }

    /**
     * After save order billing address process
     *
     * @param  Mage_Sales_Model_Order $object
     * @return $this
     */
    public function afterSave($object)
    {
        $billingAddressId = false;
        foreach ($object->getAddressesCollection() as $address) {
            /** @var Mage_Sales_Model_Order_Address $address */
            if ($address->getAddressType() == 'billing') {
                $billingAddressId = $address->getId();
            }
        }

        if ($billingAddressId) {
            $object->setBillingAddressId($billingAddressId);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getAttributeCode());
        }

        return $this;
    }
}
