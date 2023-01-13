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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invoice backend model for parent attribute
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Perform operation after save
     *
     * @param Varien_Object|Mage_Sales_Model_Order $object
     * @return $this
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        foreach ($object->getAddressesCollection() as $item) {
            $item->save();
        }
        foreach ($object->getItemsCollection() as $item) {
            $item->save();
        }
        foreach ($object->getPaymentsCollection() as $item) {
            $item->save();
        }
        foreach ($object->getStatusHistoryCollection() as $item) {
            $item->save();
        }
        foreach ($object->getRelatedObjects() as $object) {
            $object->save();
        }
        return $this;
    }
}
