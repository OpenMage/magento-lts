<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml cms block edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
