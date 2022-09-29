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
 * Adminhtml customers online filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Online_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->addField('filter_value', 'select',
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
