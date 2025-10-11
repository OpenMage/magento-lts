<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sale api resource abstract
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Api_Resource extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Default ignored attribute codes per entity type
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = [
        'global'    =>  ['entity_id', 'attribute_set_id', 'entity_type_id'],
    ];

    /**
     * Attributes map array per entity type
     *
     * @var array
     */
    protected $_attributesMap = [
        'global'    => [],
    ];

    /**
     * Update attributes for entity
     *
     * @param array $data
     * @param Mage_Core_Model_Abstract $object
     * @param string $type
     * @return $this
     */
    protected function _updateAttributes($data, $object, $type, ?array $attributes = null)
    {
        foreach ($data as $attribute => $value) {
            if ($this->_isAllowedAttribute($attribute, $type, $attributes)) {
                $object->setData($attribute, $value);
            }
        }

        return $this;
    }

    /**
     * Retrieve entity attributes values
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $type
     * @return array
     */
    protected function _getAttributes($object, $type, ?array $attributes = null)
    {
        $result = [];

        if (!is_object($object)) {
            return $result;
        }

        foreach ($object->getData() as $attribute => $value) {
            if ($this->_isAllowedAttribute($attribute, $type, $attributes)) {
                $result[$attribute] = $value;
            }
        }

        if (isset($this->_attributesMap['global'])) {
            foreach ($this->_attributesMap['global'] as $alias => $attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        if (isset($this->_attributesMap[$type])) {
            foreach ($this->_attributesMap[$type] as $alias => $attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        return $result;
    }

    /**
     * Check is attribute allowed to usage
     *
     * @param string $attributeCode
     * @param string $type
     * @return bool
     */
    protected function _isAllowedAttribute($attributeCode, $type, ?array $attributes = null)
    {
        if (!empty($attributes)
            && !(in_array($attributeCode, $attributes))
        ) {
            return false;
        }

        if (in_array($attributeCode, $this->_ignoredAttributeCodes['global'])) {
            return false;
        }

        if (isset($this->_ignoredAttributeCodes[$type])
            && in_array($attributeCode, $this->_ignoredAttributeCodes[$type])
        ) {
            return false;
        }

        return true;
    }
}
