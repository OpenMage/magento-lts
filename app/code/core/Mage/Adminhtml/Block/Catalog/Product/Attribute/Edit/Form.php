<?php
/**
 * Product attribute add/edit form block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']);
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
