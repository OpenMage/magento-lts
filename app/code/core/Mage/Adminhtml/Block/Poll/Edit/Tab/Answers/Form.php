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
 * Adminhtml poll edit answer tab form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Poll_Edit_Tab_Answers_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('add_answer_form', ['legend' => Mage::helper('poll')->__('Add New Answer')]);

        $fieldset->addField('answer_title', 'text', [
                    'name'      => 'answer_title',
                    'title'     => Mage::helper('poll')->__('Answer Title'),
                    'label'     => Mage::helper('poll')->__('Answer Title'),
                    'maxlength' => '255',
                    'no_span'   => true,
            ]
        );

        $fieldset->addField('poll_id', 'hidden', [
                    'name'      => 'poll_id',
                    'no_span'   => true,
                    'value'     => $this->getRequest()->getParam('id'),
            ]
        );

        $fieldset->addField('add_button', 'note', [
                    'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                                    ->setData([
                                        'label'     => Mage::helper('poll')->__('Add Answer'),
                                        'onclick'   => 'answers.add();',
                                        'class'     => 'add',
                                    ])->toHtml(),
                    'no_span'   => true,
            ]
        );

        $this->setForm($form);
        return $this;
    }
}
