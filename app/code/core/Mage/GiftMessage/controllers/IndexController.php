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
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 * @deprecated after 1.3.2.4
 */
class Mage_GiftMessage_IndexController extends Mage_Core_Controller_Front_Action
{
    public function saveAction()
    {
        $giftMessage = Mage::getModel('giftmessage/message');
        if ($this->getRequest()->getParam('message')) {
            $giftMessage->load($this->getRequest()->getParam('message'));
        }
        try {
            $entity = $giftMessage->getEntityModelByType($this->_getMappedType($this->getRequest()->getParam('type')));

            $giftMessage->setSender($this->getRequest()->getParam('sender'))
                ->setRecipient($this->getRequest()->getParam('recipient'))
                ->setMessage($this->getRequest()->getParam('messagetext'))
                ->save();

            $entity->load($this->getRequest()->getParam('item'))
                ->setGiftMessageId($giftMessage->getId())
                ->save();

            $this->getRequest()->setParam('message', $giftMessage->getId());
            $this->getRequest()->setParam('entity', $entity);
        } catch (Exception $e) {
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
