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
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 *
 * @deprecated after 1.3.2.4
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Block_Message_Form extends Mage_Core_Block_Template
{

    protected $_giftMessage = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('giftmessage/form.phtml');
    }

    public function getSaveUrl()
    {
        return $this->helper('giftmessage/url')->getSaveUrl(
                            $this->getRequest()->getParam('item'),
                            $this->getRequest()->getParam('type'),
                            $this->getRequest()->getParam('message'),
                            array('uniqueId'=>$this->getRequest()->getParam('uniqueId'))
        );
    }

    public function getEditUrl()
    {
        return $this->helper('giftmessage/url')->getEditUrl(
                            $this->getRequest()->getParam('entity'),
                            $this->getRequest()->getParam('type')
        );
    }

    public function getButtonUrl()
    {
        return $this->helper('giftmessage/url')->getButtonUrl(
                            $this->getRequest()->getParam('item'),
                            $this->getRequest()->getParam('type')
        );
    }

    public function getRemoveUrl()
    {
        return $this->helper('giftmessage/url')->getRemoveUrl(
                            $this->getRequest()->getParam('item'),
                            $this->getRequest()->getParam('type'),
                            array('uniqueId'=>$this->getRequest()->getParam('uniqueId'))
        );
    }

    protected function _initMessage()
    {
        $this->_giftMessage = $this->helper('giftmessage/message')->getGiftMessage(
                                            $this->getRequest()->getParam('message')
                              );
        return $this;
    }

    public function getMessage()
    {
        if(is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        return $this->_giftMessage;
    }

    public function getEscaped($value)
    {
        return $this->escapeHtml($value);
    }

    public function getEscapedForJs($value)
    {
        return addcslashes($value, "\\'\n\r\t");
    }

    public function getUniqueId()
    {
        return $this->getRequest()->getParam('uniqueId');
    }


}
