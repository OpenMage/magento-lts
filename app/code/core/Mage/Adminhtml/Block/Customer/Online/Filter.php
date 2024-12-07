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
 * Adminhtml customers online filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Online_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->addField(
            'filter_value',
            'select',
            [
                    'name' => 'filter_value',
                    'onchange' => 'this.form.submit()',
                    'values' => [
                        [
                            'label' => Mage::helper('customer')->__('All'),
                            'value' => '',
                        ],

                        [
                            'label' => Mage::helper('customer')->__('Customers Only'),
                            'value' => 'filterCustomers',
                        ],

                        [
                            'label' => Mage::helper('customer')->__('Visitors Only'),
                            'value' => 'filterGuests',
                        ]
                    ],
                    'no_span' => true
                ]
        );

        $form->setUseContainer(true);
        $form->setId('filter_form');
        $form->setMethod('post');

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
