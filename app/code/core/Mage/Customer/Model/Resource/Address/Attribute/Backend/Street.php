<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer password attribute backend
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Address_Attribute_Backend_Street extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare object for save
     *
     * @param Varien_Object|Mage_Customer_Model_Address_Abstract $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $street = $object->getStreet(-1);
        if ($street) {
            $object->implodeStreetAddress();
        }
        return $this;
    }
}
