<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Poll_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('poll_form', ['legend' => Mage::helper('poll')->__('Poll information')]);
        $fieldset->addField('poll_title', 'text', [
            'label'     => Mage::helper('poll')->__('Poll Question'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'poll_title',
        ]);

        $fieldset->addField('closed', 'select', [
            'label'     => Mage::helper('poll')->__('Status'),
            'name'      => 'closed',
            'values'    => [
                [
                    'value'     => 1,
                    'label'     => Mage::helper('poll')->__('Closed'),
                ],

                [
                    'value'     => 0,
                    'label'     => Mage::helper('poll')->__('Open'),
                ],
            ],
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'multiselect', [
                'label'     => Mage::helper('poll')->__('Visible In'),
                'required'  => true,
                'name'      => 'store_ids[]',
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                'value'     => Mage::registry('poll_data')->getStoreIds()
            ]);
        } else {
            $fieldset->addField('store_ids', 'hidden', [
                'name'      => 'store_ids[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ]);
            Mage::registry('poll_data')->setStoreIds(Mage::app()->getStore(true)->getId());
        }

        if (Mage::getSingleton('adminhtml/session')->getPollData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPollData());
            Mage::getSingleton('adminhtml/session')->setPollData(null);
        } elseif (Mage::registry('poll_data')) {
            $form->setValues(Mage::registry('poll_data')->getData());

            $fieldset->addField('was_closed', 'hidden', [
                'name'      => 'was_closed',
                'no_span'   => true,
                'value'     => Mage::registry('poll_data')->getClosed()
            ]);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
