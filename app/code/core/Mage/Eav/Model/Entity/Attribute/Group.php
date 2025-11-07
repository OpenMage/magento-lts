<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group _getResource()
 * @method string getAttributeGroupName()
 * @method Mage_Eav_Model_Entity_Attribute[] getAttributes()
 * @method int getAttributeSetId()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection getCollection()
 * @method int getDefaultId()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection getResourceCollection()
 * @method int getSortOrder()
 * @method $this setAttributeGroupName(string $value)
 * @method $this setAttributes(Mage_Eav_Model_Entity_Attribute[] $value)
 * @method $this setAttributeSetId(int $value)
 * @method $this setDefaultId(int $value)
 * @method $this setSortOrder(int $value)
 */
class Mage_Eav_Model_Entity_Attribute_Group extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_attribute_group');
    }

    /**
     * Checks if current attribute group exists
     *
     * @return bool
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
