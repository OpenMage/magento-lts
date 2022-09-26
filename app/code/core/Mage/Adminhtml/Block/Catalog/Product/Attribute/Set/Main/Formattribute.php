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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formattribute extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', ['legend'=>Mage::helper('catalog')->__('Add New Attribute')]);

        $fieldset->addField('new_attribute', 'text',
                            [
                                'label' => Mage::helper('catalog')->__('Name'),
                                'name' => 'new_attribute',
                                'required' => true,
                            ]
        );

        $fieldset->addField('submit', 'note',
                            [
                                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                                            ->setData([
                                                'label'     => Mage::helper('catalog')->__('Add Attribute'),
                                                'onclick'   => 'this.form.submit();',
                                                                                                'class' => 'add'
                                            ])
                                            ->toHtml(),
                            ]
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);
        return $this;
    }
}
