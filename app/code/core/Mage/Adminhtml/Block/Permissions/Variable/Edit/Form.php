<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml permissions variable edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Permissions_Variable_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws Exception
     */
    protected function _prepareForm()
    {
        $block = Mage::getModel('admin/variable')->load((int) $this->getRequest()->getParam('variable_id'));

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl(
                '*/*/save',
                array(
                    'variable_id' => (int) $this->getRequest()->getParam('variable_id')
                )
            ),
            'method' => 'post'
        ));
        $fieldset = $form->addFieldset(
            'variable_details', array('legend' => $this->__('Variable Details'))
        );

        $fieldset->addField('variable_name', 'text', array(
            'label' => $this->__('Variable Name'),
            'required' => true,
            'name' => 'variable_name',
        ));


        $yesno = array(
            array(
                'value' => 0,
                'label' => $this->__('No')
            ),
            array(
                'value' => 1,
                'label' => $this->__('Yes')
            ));


        $fieldset->addField('is_allowed', 'select', array(
            'name' => 'is_allowed',
            'label' => $this->__('Is Allowed'),
            'title' => $this->__('Is Allowed'),
            'values' => $yesno,
        ));

        $form->setUseContainer(true);
        $form->setValues($block->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
