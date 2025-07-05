<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Attribute add/edit form options tab
 *
 * @package    Mage_Eav
 */
abstract class Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('eav/attribute/options.phtml');
    }

    /**
     * Preparing layout, adding buttons
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('eav')->__('Delete'),
                    'class' => 'delete delete-option',
                ]),
        );

        $this->setChild(
            'add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('eav')->__('Add Option'),
                    'class' => 'add',
                    'id'    => 'add_new_option_button',
                ]),
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve HTML of delete button
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrieve HTML of add button
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }

    /**
     * Retrieve attribute option values if attribute input type select or multiselect
     *
     * @return array
     */
    public function getOptionValues()
    {
        $attributeType = $this->getAttributeObject()->getFrontendInput();
        $defaultValues = $this->getAttributeObject()->getDefaultValue();
        if ($attributeType === 'select' || $attributeType === 'multiselect') {
            $defaultValues = explode(',', (string) $defaultValues);
        } else {
            $defaultValues = [];
        }

        switch ($attributeType) {
            case 'select':
                $inputType = 'radio';
                break;
            case 'multiselect':
                $inputType = 'checkbox';
                break;
            default:
                $inputType = '';
                break;
        }

        $values = $this->getData('option_values');
        if (is_null($values)) {
            $values = [];
            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
                ->setPositionOrder('desc', true)
                ->load();

            $helper = Mage::helper('core');
            /** @var Mage_Eav_Model_Entity_Attribute_Option $option */
            foreach ($optionCollection as $option) {
                $value = [];
                if (in_array($option->getId(), $defaultValues)) {
                    $value['checked'] = 'checked="checked"';
                } else {
                    $value['checked'] = '';
                }

                $value['intype'] = $inputType;
                $value['id'] = $option->getId();
                $value['sort_order'] = $option->getSortOrder();
                foreach ($this->getStores() as $store) {
                    $storeValues = $this->getStoreOptionValues($store->getId());
                    $value['store' . $store->getId()] = isset($storeValues[$option->getId()])
                        ? $helper->escapeHtml($storeValues[$option->getId()]) : '';
                }
                if ($this->isConfigurableSwatchesEnabled()) {
                    $value['swatch'] = $option->getSwatchValue();
                }
                $values[] = new Varien_Object($value);
            }
            $this->setData('option_values', $values);
        }
        return $values;
    }

    /**
     * Retrieve frontend labels of attribute for each store
     *
     * @return array
     */
    public function getLabelValues()
    {
        $values = [];
        $frontendLabel = $this->getAttributeObject()->getFrontend()->getLabel();
        if (is_array($frontendLabel)) {
            return $frontendLabel;
        }
        $values[0] = $frontendLabel;
        $storeLabels = $this->getAttributeObject()->getStoreLabels();
        foreach ($this->getStores() as $store) {
            if ($store->getId() != 0) {
                $values[$store->getId()] = $storeLabels[$store->getId()] ?? '';
            }
        }
        return $values;
    }

    /**
     * Retrieve attribute option values for given store id
     *
     * @param int $storeId
     * @return array
     */
    public function getStoreOptionValues($storeId)
    {
        $values = $this->getData('store_option_values_' . $storeId);
        if (is_null($values)) {
            $values = [];
            $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
                ->setStoreFilter($storeId, false)
                ->load();
            /** @var Mage_Eav_Model_Entity_Attribute_Option $item */
            foreach ($valuesCollection as $item) {
                $values[$item->getId()] = $item->getValue();
            }
            $this->setData('store_option_values_' . $storeId, $values);
        }
        return $values;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttributeObject()
    {
        return Mage::registry('entity_attribute');
    }

    /**
     * Check if configurable swatches module is enabled and attribute is swatch type
     */
    public function isConfigurableSwatchesEnabled(): bool
    {
        return $this->isModuleEnabled('Mage_ConfigurableSwatches')
            && Mage::helper('configurableswatches')->attrIsSwatchType($this->getAttributeObject());
    }
}
