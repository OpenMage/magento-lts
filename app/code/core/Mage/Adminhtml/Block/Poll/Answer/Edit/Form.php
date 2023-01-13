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
 * Adminhtml poll answer edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Poll_Answer_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('edit_answer_form', ['legend' => Mage::helper('poll')->__('Edit Poll Answer')]);

        $fieldset->addField('answer_title', 'text', [
                    'name'      => 'answer_title',
                    'title'     => Mage::helper('poll')->__('Answer Title'),
                    'label'     => Mage::helper('poll')->__('Answer Title'),
                    'required'  => true,
                    'class'     => 'required-entry',
            ]);

        $fieldset->addField('votes_count', 'text', [
                    'name'      => 'votes_count',
                    'title'     => Mage::helper('poll')->__('Votes Count'),
                    'label'     => Mage::helper('poll')->__('Votes Count'),
                    'class'     => 'validate-not-negative-number'
            ]);

        $fieldset->addField('poll_id', 'hidden', [
                    'name'      => 'poll_id',
                    'no_span'   => true,
            ]);

        $form->setValues(Mage::registry('answer_data')->getData());
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/*/save', ['id' => Mage::registry('answer_data')->getAnswerId()]));
        $this->setForm($form);
        return $this;
    }
}
