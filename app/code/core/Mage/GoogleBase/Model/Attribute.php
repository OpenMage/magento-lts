<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Attributes Model
 *
 * @method Mage_GoogleBase_Model_Resource_Attribute _getResource()
 * @method Mage_GoogleBase_Model_Resource_Attribute getResource()
 * @method int getAttributeId()
 * @method Mage_GoogleBase_Model_Attribute setAttributeId(int $value)
 * @method string getGbaseAttribute()
 * @method Mage_GoogleBase_Model_Attribute setGbaseAttribute(string $value)
 * @method int getTypeId()
 * @method Mage_GoogleBase_Model_Attribute setTypeId(int $value)
 *
 * @deprecated after 1.5.1.0
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Attribute extends Mage_Core_Model_Abstract
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array(
        'custom_design','custom_design_from','custom_design_to','custom_layout_update',
        'gift_message_available','news_from_date','news_to_date','options_container',
        'price_view','sku_type'
    );

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = array('hidden', 'media_image', 'image', 'gallery');

    protected function _construct()
    {
        $this->_init('googlebase/attribute');
    }

    public function getAllowedAttributes($setId)
    {
        $attributes = Mage::getModel('catalog/product')->getResource()
                ->loadAllAttributes()
                ->getSortedAttributes($setId);

        $result = array();
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ($attribute->isInSet($setId) && $this->_isAllowedAttribute($attribute)) {
                $list[$attribute->getAttributeId()] = $attribute;
                $titles[$attribute->getAttributeId()] = $attribute->getFrontendLabel();
            }
        }
        asort($titles);
        $result = array();
        foreach ($titles as $attributeId => $label) {
            $result[$attributeId] = $list[$attributeId];
        }
        return $result;
    }

    /**
     * Check if attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute)
    {
        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes)
               && $attribute->getFrontendLabel() != "";
    }

    /**
     * Return Google Base Attribute Type By Product Attribute
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string Google Base Attribute Type
     */
    public function getGbaseAttributeType($attribute)
    {
        $typesMapping = array(
//            'date'       => 'dateTime',
            'price'      => 'floatUnit',
            'decimal'    => 'numberUnit',
        );
        if (isset($typesMapping[$attribute->getFrontendInput()])) {
            return $typesMapping[$attribute->getFrontendInput()];
        } elseif (isset($typesMapping[$attribute->getBackendType()])) {
            return $typesMapping[$attribute->getBackendType()];
        } else {
            return Mage_GoogleBase_Model_Service_Item::DEFAULT_ATTRIBUTE_TYPE;
        }
    }
}
