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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Messages block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Messages extends Mage_Core_Block_Template
{
    /**
     * Messages collection
     *
     * @var Mage_Core_Model_Message_Collection
     */
    protected $_messages;

    public function _prepareLayout()
    {
        $this->addMessages(Mage::getSingleton('core/session')->getMessages(true));

        parent::_prepareLayout();
    }

    /**
     * Set messages collection
     *
     * @param   Mage_Core_Model_Message_Collection $messages
     * @return  Mage_Core_Block_Messages
     */
    public function setMessages(Mage_Core_Model_Message_Collection $messages)
    {
        $this->_messages = $messages;
        return $this;
    }

    public function addMessages(Mage_Core_Model_Message_Collection $messages)
    {
        foreach ($messages->getItems() as $message) {
            $this->getMessageCollection()->add($message);
        }
        return $this;
    }

    /**
     * Retrieve messages collection
     *
     * @return Mage_Core_Model_Message_Collection
     */
    public function getMessageCollection()
    {
        if (!($this->_messages instanceof Mage_Core_Model_Message_Collection)) {
            $this->_messages = Mage::getModel('core/message_collection');
        }
        return $this->_messages;
    }

    /**
     * Adding new message to message collection
     *
     * @param   Mage_Core_Model_Message_Abstract $message
     * @return  Mage_Core_Block_Messages
     */
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        $this->getMessageCollection()->add($message);
        return $this;
    }

    /**
     * Adding new error message
     *
     * @param   string $message
     * @return  Mage_Core_Block_Messages
     */
    public function addError($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->error($message));
        return $this;
    }

    /**
     * Adding new warning message
     *
     * @param   string $message
     * @return  Mage_Core_Block_Messages
     */
    public function addWarning($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->warning($message));
        return $this;
    }

    /**
     * Adding new nitice message
     *
     * @param   string $message
     * @return  Mage_Core_Block_Messages
     */
    public function addNotice($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->notice($message));
        return $this;
    }

    /**
     * Adding new success message
     *
     * @param   string $message
     * @return  Mage_Core_Block_Messages
     */
    public function addSuccess($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->success($message));
        return $this;
    }

    /**
     * Retrieve messages array by message type
     *
     * @param   string $type
     * @return  array
     */
    public function getMessages($type=null)
    {
        return $this->getMessageCollection()->getItems($type);
    }

    /**
     * Retrieve messages in HTML format
     *
     * @param   string $type
     * @return  string
     */
    public function getHtml($type=null)
    {
        $html = '<ul id="admin_messages">';
        foreach ($this->getMessages($type) as $message) {
            $html.= '<li class="'.$message->getType().'-msg">'.$message->getText().'</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * Retrieve messages in HTML format grouped by type
     *
     * @param   string $type
     * @return  string
     */
    public function getGroupedHtml()
    {
        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );
        $html = '';
        foreach ($types as $type) {
            if ( $messages = $this->getMessages($type) ) {
                if ( !$html ) {
                    $html .= '<ul class="messages">';
                }
                $html .= '<li class="' . $type . '-msg">';
                $html .= '<ul>';

                foreach ( $messages as $message ) {
                    $html.= '<li>';
                    $html.= $message->getText();
                    $html.= '</li>';
                }
                $html .= '</ul>';
                $html .= '</li>';
            }
        }
        if ( $html) {
            $html .= '</ul>';
        }
        return $html;
    }

    protected function _toHtml()
    {
        return $this->getGroupedHtml();
    }
}
