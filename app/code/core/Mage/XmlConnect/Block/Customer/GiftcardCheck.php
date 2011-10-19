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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Check Gift card xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_GiftcardCheck extends Enterprise_GiftCardAccount_Block_Check
{
    /**
     * Render gift card info xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $card = $this->getCard();
        if ($card->getId()) {
            /** @var $xmlModel Mage_XmlConnect_Model_Simplexml_Element */
            $xmlModel = Mage::getModel('xmlconnect/simplexml_element', '<gift_card_account></gift_card_account>');

            $balance = Mage::helper('core')->currency($card->getBalance(), true, false);

            $result[] = $this->__("Gift Card: %s", $card->getCode());
            $result[] = $this->__('Current Balance: %s', $balance);

            if ($card->getDateExpires()) {
                $result[] = $this->__('Expires: %s', $this->formatDate($card->getDateExpires(), 'short'));
            }
            $xmlModel->addCustomChild('info', implode(PHP_EOL, $result));
        } else {
            $xmlModel = Mage::getModel('xmlconnect/simplexml_element', '<message></message>');
            $xmlModel->addCustomChild('status', Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_ERROR);
            $xmlModel->addCustomChild('text', $this->__('Wrong or expired Gift Card Code.'));
        }

        return $xmlModel->asNiceXml();
    }
}
