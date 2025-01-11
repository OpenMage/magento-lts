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
 * Product attribute edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'catalog_product_attribute';

        parent::__construct();

        if ($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            $this->_addButton(
                'close',
                [
                    'label'     => Mage::helper('catalog')->__('Close Window'),
                    'class'     => 'cancel',
                    'onclick'   => 'window.close()',
                    'level'     => -1,
                ],
            );
        } else {
            $this->_addButton(
                'save_and_edit_button',
                [
                    'label'     => Mage::helper('catalog')->__('Save and Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit()',
                    'class'     => 'save',
                ],
                100,
            );
        }

        $this->_updateButton('save', 'label', Mage::helper('catalog')->__('Save Attribute'));
        $this->_updateButton('save', 'onclick', 'saveAttribute()');

        if (!Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('catalog')->__('Delete Attribute'));
        }
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('entity_attribute')->getId()) {
            $frontendLabel = Mage::registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return Mage::helper('catalog')->__('Edit Product Attribute "%s"', $this->escapeHtml($frontendLabel));
        }
        return Mage::helper('catalog')->__('New Product Attribute');
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/save', ['_current' => true, 'back' => null]);
    }
}
