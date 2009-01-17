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
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Entity_Attribute extends Mage_Eav_Model_Entity_Attribute_Abstract
{
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
        // prevent changing attribute scope, if used in configurable products
        if (isset($this->_origData['is_global'])) {
            if (!isset($this->_data['is_global'])) {
                Mage::throwException('0_o');
            }
            if (($this->_data['is_global'] != $this->_origData['is_global'])
                && $this->_getResource()->isUsedBySuperProducts($this)) {
                Mage::throwException(Mage::helper('eav')->__('Scope must not be changed, because the attribute is used in configurable products.'));
            }
        }

        if ($this->getBackendType() == 'datetime') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('eav/entity_attribute_backend_datetime');
            }

            if (!$this->getFrontendModel()) {
                $this->setFrontendModel('eav/entity_attribute_frontend_datetime');
            }
        }

        if ($this->getBackendType() == 'gallery') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('eav/entity_attribute_backend_media');
            }
        }

        if ($this->getFrontendInput() == 'price') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('catalog/product_attribute_backend_price');
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
            Mage::throwException(Mage::helper('eav')->__('Attribute used in configurable products.'));
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

            default:
                Mage::throwException('Unknown frontend input type');
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

            default:
                Mage::throwException('Unknown frontend input type');
        }

        return $field;
    }

}
