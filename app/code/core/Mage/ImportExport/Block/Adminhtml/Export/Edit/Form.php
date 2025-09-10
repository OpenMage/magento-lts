<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Export edit form block
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Block_Adminhtml_Export_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML.
     *
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id'     => 'edit_form',
            'action' => $this->getUrl('*/*/getFilter'),
            'method' => 'post',
        ]);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('importexport')->__('Export Settings')]);
        $fieldset->addField('entity', 'select', [
            'name'     => 'entity',
            'title'    => Mage::helper('importexport')->__('Entity Type'),
            'label'    => Mage::helper('importexport')->__('Entity Type'),
            'required' => false,
            'onchange' => 'editForm.getFilter();',
            'values'   => Mage::getModel('importexport/source_export_entity')->toOptionArray(),
        ]);
        $fieldset->addField('file_format', 'select', [
            'name'     => 'file_format',
            'title'    => Mage::helper('importexport')->__('Export File Format'),
            'label'    => Mage::helper('importexport')->__('Export File Format'),
            'required' => false,
            'values'   => Mage::getModel('importexport/source_export_format')->toOptionArray(),
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
