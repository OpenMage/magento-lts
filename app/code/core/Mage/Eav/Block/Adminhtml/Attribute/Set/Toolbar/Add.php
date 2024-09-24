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
class Mage_Eav_Block_Adminhtml_Attribute_Set_Toolbar_Add extends Mage_Adminhtml_Block_Template
{
    protected function _construct(): void
    {
        $this->setTemplate('eav/attribute/set/toolbar/add.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('eav')->__('Save Attribute Set'),
                    'onclick'   => 'if (addSet.submit()) disableElements(\'save\');',
                    'class' => 'save'
            ])
        );
        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('eav')->__('Back'),
                    'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/') . '\')',
                    'class' => 'back'
            ])
        );

        $this->setChild(
            'setForm',
            $this->getLayout()->createBlock('eav/adminhtml_attribute_set_main_formset')
        );
        return parent::_prepareLayout();
    }

    protected function _getHeader(): string
    {
        return Mage::helper('eav')->__('Add New Attribute Set');
    }

    protected function getSaveButtonHtml(): string
    {
        return $this->getChildHtml('save_button');
    }

    protected function getBackButtonHtml(): string
    {
        return $this->getChildHtml('back_button');
    }

    protected function getFormHtml(): string
    {
        return $this->getChildHtml('setForm');
    }

    protected function getFormId(): string
    {
        return $this->getChild('setForm')->getForm()->getId();
    }
}
