<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_storeModel;
    protected $_attributes;
    protected $_addMapButtonHtml;
    protected $_removeMapButtonHtml;
    protected $_shortDateFormat;

    /**
     * @var array
     */
    protected $_filterStores;

    /**
     * Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/convert/profile/wizard.phtml');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setCanLoadCalendarJs(true);
        }
        return $this;
    }

    /**
     * @param string $entityType
     * @return array|string[]
     */
    public function getAttributes($entityType)
    {
        if (!isset($this->_attributes[$entityType])) {
            $attributes = [];
            switch ($entityType) {
                case 'product':
                    $attributes = Mage::getSingleton('catalog/convert_parser_product')
                        ->getExternalAttributes();
                    break;

                case 'customer':
                    $attributes = Mage::getSingleton('customer/convert_parser_customer')
                        ->getExternalAttributes();
                    break;
            }

            array_splice($attributes, 0, 0, ['' => $this->__('Choose an attribute')]);
            $this->_attributes[$entityType] = $attributes;
        }
        return $this->_attributes[$entityType];
    }

    /**
     * @param string $key
     * @param string $default
     * @param string|null $defaultNew
     * @return string
     */
    public function getValue($key, $default = '', $defaultNew = null)
    {
        if ($defaultNew !== null) {
            if ($this->getProfileId() == 0) {
                $default = $defaultNew;
            }
        }

        $value = $this->getData($key);
        return $this->escapeHtml($value !== null && strlen($value) > 0 ? $value : $default);
    }

    /**
     * @param string $key
     * @param string|bool|int $value
     * @return string
     *
     * @todo check remove int from param value
     */
    public function getSelected($key, $value)
    {
        return $this->getData($key) == $value ? 'selected="selected"' : '';
    }

    public function getChecked($key)
    {
        return $this->getData($key) ? 'checked="checked"' : '';
    }

    /**
     * @param string $entityType
     * @return array
     */
    public function getMappings($entityType)
    {
        $maps = $this->getData('gui_data/map/' . $entityType . '/db');
        return $maps ?: [];
    }

    /**
     * @return string
     */
    public function getAddMapButtonHtml()
    {
        if (!$this->_addMapButtonHtml) {
            $this->_addMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('add')->setLabel($this->__('Add Field Mapping'))
                ->setOnClick('addFieldMapping()')->toHtml();
        }
        return $this->_addMapButtonHtml;
    }

    /**
     * @return string
     */
    public function getRemoveMapButtonHtml()
    {
        if (!$this->_removeMapButtonHtml) {
            $this->_removeMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('delete')->setLabel($this->__('Remove'))
                ->setOnClick('removeFieldMapping(this)')->toHtml();
        }
        return $this->_removeMapButtonHtml;
    }

    /**
     * @return array
     */
    public function getProductTypeFilterOptions()
    {
        $options = Mage::getSingleton('catalog/product_type')->getOptionArray();
        array_splice($options, 0, 0, ['' => $this->__('Any Type')]);
        return $options;
    }

    /**
     * @return array
     */
    public function getProductAttributeSetFilterOptions()
    {
        $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $opt = ['' => $this->__('Any Attribute Set')];
        if ($options) {
            foreach ($options as $index => $value) {
                $opt[$index] = $value;
            }
        }
        return $opt;
    }

    /**
     * @return array
     */
    public function getProductVisibilityFilterOptions()
    {
        $options = Mage::getSingleton('catalog/product_visibility')->getOptionArray();
        array_splice($options, 0, 0, ['' => $this->__('Any Visibility')]);
        return $options;
    }

    /**
     * @return array
     */
    public function getProductStatusFilterOptions()
    {
        $options = Mage::getSingleton('catalog/product_status')->getOptionArray();
        array_splice($options, 0, 0, ['' => $this->__('Any Status')]);
        return $options;
    }

    /**
     * @return array
     */
    public function getStoreFilterOptions()
    {
        if (!$this->_filterStores) {
            $this->_filterStores = [];
            foreach (Mage::getConfig()->getNode('stores')->children() as $storeNode) {
                $this->_filterStores[$storeNode->getName()] = (string)$storeNode->system->store->name;
            }
        }
        return $this->_filterStores;
    }

    /**
     * @return array
     */
    public function getCustomerGroupFilterOptions()
    {
        $options = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', ['gt' => 0])
            ->load()
            ->toOptionHash();

        array_splice($options, 0, 0, ['' => $this->__('Any Group')]);
        return $options;
    }

    /**
     * @return array
     */
    public function getCountryFilterOptions()
    {
        $options = Mage::getResourceModel('directory/country_collection')
            ->load()->toOptionArray(false);
        array_unshift($options, ['value' => '', 'label' => Mage::helper('adminhtml')->__('All countries')]);
        return $options;
    }

    /**
     * Retrieve system store model
     *
     * @return Mage_Adminhtml_Model_System_Store
     */
    protected function _getStoreModel()
    {
        if (is_null($this->_storeModel)) {
            $this->_storeModel = Mage::getSingleton('adminhtml/system_store');
        }
        return $this->_storeModel;
    }

    /**
     * @return array
     */
    public function getWebsiteCollection()
    {
        return $this->_getStoreModel()->getWebsiteCollection();
    }

    /**
     * @return array
     */
    public function getGroupCollection()
    {
        return $this->_getStoreModel()->getGroupCollection();
    }

    /**
     * @return array
     */
    public function getStoreCollection()
    {
        return $this->_getStoreModel()->getStoreCollection();
    }

    /**
     * @return string
     */
    public function getShortDateFormat()
    {
        if (!$this->_shortDateFormat) {
            $this->_shortDateFormat = Mage::app()->getLocale()->getDateStrFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
            );
        }
        return $this->_shortDateFormat;
    }
}
