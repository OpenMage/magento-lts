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
 * Admin poll answer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Poll_Answer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll_answer';

        if ($this->getRequest()->getParam($this->_objectId)) {
            $answerData = Mage::getModel('poll/poll_answer')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('answer_data', $answerData);
        }

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/poll/edit', ['id' => $answerData->getPollId(), 'tab' => 'answers_section']) . '\');');
        $this->_updateButton('save', 'label', Mage::helper('poll')->__('Save Answer'));
        $this->_updateButton('delete', 'label', Mage::helper('poll')->__('Delete Answer'));
    }

    public function getHeaderText()
    {
        return Mage::helper('poll')->__("Edit Answer '%s'", $this->escapeHtml(Mage::registry('answer_data')->getAnswerTitle()));
    }
}
