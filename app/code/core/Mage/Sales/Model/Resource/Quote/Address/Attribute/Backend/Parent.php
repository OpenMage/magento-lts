<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 *Quote address attribute backend parent resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Save items collection and shipping rates collection
     *
     * @param Mage_Sales_Model_Quote_Address|Varien_Object $object
     * @return $this
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        $object->getItemsCollection()->save();
        $object->getShippingRatesCollection()->save();

        return $this;
    }
}
