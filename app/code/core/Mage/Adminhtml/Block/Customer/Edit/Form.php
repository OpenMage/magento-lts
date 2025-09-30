<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customer edit form block
 *
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data',
        ]);

        $customer = Mage::registry('current_customer');

        if ($customer->getId()) {
            $form->addField('entity_id', 'hidden', [
                'name' => 'customer_id',
            ]);
            $form->setValues($customer->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
