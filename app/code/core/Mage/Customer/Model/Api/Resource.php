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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer abstract API resource
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param array|null $filter
     * @return bool
     */
    protected function _isAllowedAttribute($attribute, array $filter = null)
    {
        if (!is_null($filter)
            && !(in_array($attribute->getAttributeCode(), $filter)
                  || in_array($attribute->getAttributeId(), $filter))) {
            return false;
        }

        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes);
    }

    /**
     * Return list of allowed attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @param array|null $filter
     * @return array
     */
    public function getAllowedAttributes($entity, array $filter = null)
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
