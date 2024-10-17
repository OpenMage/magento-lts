<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Block_Adminhtml_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_blockGroup = 'eav';
        $this->_controller = 'adminhtml_attribute';

        parent::__construct();

        $this->_addButton(
            'save_and_edit_button',
            [
                'label'     => Mage::helper('eav')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save'
            ],
            100
        );

        $this->_updateButton('save', 'label', Mage::helper('eav')->__('Save Attribute'));
        $this->_updateButton('save', 'onclick', 'saveAttribute()');

        if (!Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('eav')->__('Delete Attribute'));
        }
    }

    public function getHeaderText(): string
    {
        if (Mage::registry('entity_attribute')->getId()) {
            $frontendLabel = Mage::registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return Mage::helper('eav')->__('Edit %s Attribute "%s"', Mage::helper('eav')->formatTypeCode(Mage::registry('entity_type')), $this->escapeHtml($frontendLabel));
        } else {
            return Mage::helper('eav')->__('New %s Attribute', Mage::helper('eav')->formatTypeCode(Mage::registry('entity_type')));
        }
    }

    public function getValidationUrl(): string
    {
        return $this->getUrl('*/*/validate', ['_current' => true]);
    }

    public function getSaveUrl(): string
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => null]);
    }

    public function getHeaderCssClass(): string
    {
        return 'icon-head head-eav-attribute';
    }
}
