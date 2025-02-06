<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api2
 */

/**
 * Attribute edit form block
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Attribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        $form   = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
        ]);

        $form->setAction($this->getUrl('*/*/save', ['type' => $this->getRequest()->getParam('type')]))
            ->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
