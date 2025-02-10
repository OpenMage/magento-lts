<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_GiftMessage
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
