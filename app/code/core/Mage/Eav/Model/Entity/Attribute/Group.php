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
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection getCollection()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection getResourceCollection()
 *
 * @method Mage_Eav_Model_Entity_Attribute[] getAttributes()
 * @method $this setAttributes(Mage_Eav_Model_Entity_Attribute[] $value)
 * @method int getAttributeSetId()
 * @method $this setAttributeSetId(int $value)
 * @method string getAttributeGroupName()
 * @method $this setAttributeGroupName(string $value)
 * @method $this setDefaultId(int $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method int getDefaultId()
 */
class Mage_Eav_Model_Entity_Attribute_Group extends Mage_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav/entity_attribute_group');
    }

    /**
     * Checks if current attribute group exists
     *
     * @return boolean
     */
    public function itemExists()
    {
        return $this->_getResource()->itemExists($this);
    }

    /**
     * Delete groups
     *
     * @return $this
     */
    public function deleteGroups()
    {
        return $this->_getResource()->deleteGroups($this);
    }
}
