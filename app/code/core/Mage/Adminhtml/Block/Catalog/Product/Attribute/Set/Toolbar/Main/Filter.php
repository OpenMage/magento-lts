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
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Main_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $collection = Mage::getModel('eav/entity_attribute_set')
            ->getResourceCollection()
            ->load()
            ->toOptionArray();

        $form->addField(
            'set_switcher',
            'select',
            [
                'name' => 'set_switcher',
                'required' => true,
                'class' => 'left-col-block',
                'no_span' => true,
                'values' => $collection,
                'onchange' => 'this.form.submit()',
            ],
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);
        return $this;
    }
}
