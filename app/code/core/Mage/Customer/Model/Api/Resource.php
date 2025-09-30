<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer abstract API resource
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Api_Resource extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = ['entity_id', 'attribute_set_id', 'entity_type_id'];

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = [];

    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return bool
     */
    protected function _isAllowedAttribute($attribute, ?array $filter = null)
    {
        if (!is_null($filter)
            && !(in_array($attribute->getAttributeCode(), $filter)
                  || in_array($attribute->getAttributeId(), $filter))
        ) {
            return false;
        }

        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes);
    }

    /**
     * Return list of allowed attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @return array
     */
    public function getAllowedAttributes($entity, ?array $filter = null)
    {
        $attributes = $entity->getResource()
                        ->loadAllAttributes($entity)
                        ->getAttributesByCode();
        $result = [];
        foreach ($attributes as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $filter)) {
                $result[$attribute->getAttributeCode()] = $attribute;
            }
        }

        return $result;
    }
}
