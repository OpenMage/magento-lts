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
 * @package     Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 *
 * @deprecated after 1.3.2.4
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Block_Message_Helper extends Mage_Core_Block_Template
{

    protected $_entity = null;
    protected $_type   = null;
    protected $_giftMessage = null;

    static protected $_scriptIncluded = false;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('giftmessage/helper.phtml');
    }

    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function hasGiftMessage()
    {
        return $this->getEntity()->getGiftMessageId() > 0;
    }

    public function setScriptIncluded($value)
    {
        self::$_scriptIncluded = $value;
        return $this;
    }

    public function getScriptIncluded()
    {
        return self::$_scriptIncluded;
    }

    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }

    public function getEditUrl()
    {
        return $this->helper('giftmessage/url')->getEditUrl($this->getEntity(), $this->getType());
    }

    protected function _initMessage()
    {
        $this->_giftMessage = $this->helper('giftmessage/message')->getGiftMessage(
                                            $this->getEntity()->getGiftMessageId()
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

}
