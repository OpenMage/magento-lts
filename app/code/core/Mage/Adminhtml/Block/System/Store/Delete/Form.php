<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml cms block edit form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Store_Delete_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('store_delete_form');
        $this->setTitle(Mage::helper('cms')->__('Block Information'));
    }

    protected function _prepareForm()
    {
        $dataObject = $this->getDataObject();

        $form = new Varien_Data_Form(['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']);

        $form->setHtmlIdPrefix('store_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('core')->__('Backup Options'), 'class' => 'fieldset-wide']);

        $fieldset->addField('item_id', 'hidden', [
            'name'  => 'item_id',
            'value' => $dataObject->getId(),
        ]);

        $fieldset->addField('create_backup', 'select', [
            'label'     => Mage::helper('adminhtml')->__('Create DB Backup'),
            'title'     => Mage::helper('adminhtml')->__('Create DB Backup'),
            'name'      => 'create_backup',
            'options'   => [
                '1' => Mage::helper('adminhtml')->__('Yes'),
                '0' => Mage::helper('adminhtml')->__('No'),
            ],
            'value'     => '1',
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
