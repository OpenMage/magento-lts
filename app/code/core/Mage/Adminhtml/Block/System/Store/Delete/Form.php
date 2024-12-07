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
 * Adminhtml cms block edit form
 *
 * @category   Mage
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
