<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formgroup extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', ['legend' => Mage::helper('catalog')->__('Add New Group')]);

        $fieldset->addField(
            'attribute_group_name',
            'text',
            [
                'label' => Mage::helper('catalog')->__('Name'),
                'name' => 'attribute_group_name',
                'required' => true,
            ],
        );

        $fieldset->addField(
            'submit',
            'note',
            [
                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData([
                                'label'     => Mage::helper('catalog')->__('Add Group'),
                                'onclick'   => 'this.form.submit();',
                                'class' => 'add',
                            ])
                            ->toHtml(),
            ],
        );

        $fieldset->addField(
            'attribute_set_id',
            'hidden',
            [
                'name' => 'attribute_set_id',
                'value' => $this->_getSetId(),
            ],
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/catalog_product_group/save'));
        $this->setForm($form);
        return $this;
    }

    protected function _getSetId()
    {
        return ((int) $this->getRequest()->getParam('id') > 0)
                    ? (int) $this->getRequest()->getParam('id')
                    : Mage::getSingleton('eav/config')->getEntityType(Mage::registry('entityType'))
                        ->getDefaultAttributeSetId();
    }
}
