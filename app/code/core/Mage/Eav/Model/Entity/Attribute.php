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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV Entity attribute model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Attribute extends Mage_Eav_Model_Entity_Attribute_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_entity_attribute';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getAttribute() in this case
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    const CACHE_TAG         = 'EAV_ATTRIBUTE';
    protected $_cacheTag    = 'EAV_ATTRIBUTE';

    protected function _getDefaultBackendModel()
    {
        switch ($this->getAttributeCode()) {
            case 'created_at':
                return 'eav/entity_attribute_backend_time_created';

            case 'updated_at':
                return 'eav/entity_attribute_backend_time_updated';

            case 'store_id':
                return 'eav/entity_attribute_backend_store';

            case 'increment_id':
                return 'eav/entity_attribute_backend_increment';
        }



        return parent::_getDefaultBackendModel();
    }

    protected function _getDefaultFrontendModel()
    {
        return parent::_getDefaultFrontendModel();
    }

    protected function _getDefaultSourceModel()
    {
        switch ($this->getAttributeCode()) {
            case 'store_id':
                return 'eav/entity_attribute_source_store';
        }
        return parent::_getDefaultSourceModel();
    }

    public function deleteEntity()
    {
        return $this->_getResource()->deleteEntity($this);
    }

    protected function _beforeSave()
    {
        // prevent overriding product data
        if (isset($this->_data['attribute_code'])
            && Mage::getModel('catalog/product')->isReservedAttribute($this)) {
            Mage::throwException(Mage::helper('eav')->__('The attribute code \'%s\' is reserved by system. Please try another attribute code.', $this->_data['attribute_code']));
        }

        $defaultValue = $this->getDefaultValue();
        $hasDefaultValue = ((string)$defaultValue != '');

        if ($this->getBackendType() == 'decimal' && $hasDefaultValue) {
            if (!Zend_Locale_Format::isNumber($defaultValue, array('locale' => Mage::app()->getLocale()->getLocaleCode()))) {
                throw new Exception('Invalid default decimal value.');
            }
            try {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $this->setDefaultValue($filter->filter($defaultValue));
            } catch (Exception $e) {
                throw new Exception('Invalid default decimal value.');
            }
        }

        if ($this->getBackendType() == 'datetime') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('eav/entity_attribute_backend_datetime');
            }

            if (!$this->getFrontendModel()) {
                $this->setFrontendModel('eav/entity_attribute_frontend_datetime');
            }

            // save default date value as timestamp
            if ($hasDefaultValue) {
                $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                try {
                    $defaultValue = Mage::app()->getLocale()->date($defaultValue, $format, null, false)->toValue();
                    $this->setDefaultValue($defaultValue);
                } catch (Exception $e) {
                    throw new Exception('Invalid default date.');
                }
            }
        }

        if ($this->getBackendType() == 'gallery') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('eav/entity_attribute_backend_media');
            }
        }

        return parent::_beforeSave();
    }

    protected function _afterSave()
    {
        $this->_getResource()->saveInSetIncluding($this);

        return parent::_afterSave();
    }

    protected function _beforeDelete()
    {
        if ($this->_getResource()->isUsedBySuperProducts($this)) {
            Mage::throwException(Mage::helper('eav')->__('This attribute is used in configurable products.'));
        }
        return parent::_beforeDelete();
    }

    /**
     * Detect backend storage type using frontend input type
     *
     * @return string backend_type field value
     * @param string $type frontend_input field value
     */
    public function getBackendTypeByInput($type)
    {
        switch ($type) {
            case 'text':
            case 'gallery':
            case 'media_image':
            case 'multiselect':
                return 'varchar';

            case 'image':
            case 'textarea':
                return 'text';

            case 'date':
                return 'datetime';

            case 'select':
            case 'boolean':
                return 'int';


            case 'price':
                return 'decimal';
/*
            default:
                Mage::dispatchEvent('eav_attribute_get_backend_type_by_input', array('model'=>$this, 'type'=>$type));
                if ($this->hasBackendTypeByInput()) {
                    return $this->getData('backend_type_by_input');
                }
                Mage::throwException('Unknown frontend input type');
*/
        }
    }

    /**
     * Detect default value using frontend input type
     *
     * @return string default_value field value
     * @param string $type frontend_input field name
     */
    public function getDefaultValueByInput($type)
    {
        $field = '';
        switch ($type) {
            case 'select':
            case 'gallery':
            case 'media_image':
            case 'multiselect':
                return '';

            case 'text':
            case 'price':
            case 'image':
                $field = 'default_value_text';
                break;

            case 'textarea':
                $field = 'default_value_textarea';
                break;

            case 'date':
                $field = 'default_value_date';
                break;

            case 'boolean':
                $field = 'default_value_yesno';
                break;
/*
            default:
                Mage::dispatchEvent('eav_attribute_get_default_value_by_input', array('model'=>$this, 'type'=>$type));
                if ($this->hasBackendTypeByInput()) {
                    return $this->getData('backend_type_by_input');
                }
                Mage::throwException('Unknown frontend input type');
*/
        }

        return $field;
    }
    public function getAttributeCodesByFrontendType($type)
    {
        return $this->getResource()->getAttributeCodesByFrontendType($type);
    }

    /**
     * Return array of labels of stores
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->getData('store_labels')) {
            $this->setData('store_labels', $this->getResource()->getStoreLabelsByAttributeId($this->getId()));
        }
        return $this->getData('store_labels');
    }

    /**
     * Return store label of attribute
     *
     * @return string
     */
    public function getStoreLabel()
    {
        return $this->getData('store_label');
    }
}
