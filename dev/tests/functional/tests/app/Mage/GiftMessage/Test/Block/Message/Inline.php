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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\GiftMessage\Test\Block\Message;

use Mage\GiftMessage\Test\Fixture\GiftMessage;
use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Mage\GiftMessage\Test\Block\Message\Inline\GiftMessageForm;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Checkout add gift options.
 */
class Inline extends Form
{
    /**
     * Selector for gift message on item form.
     *
     * @var string
     */
    protected $giftMessageItemForm = '//li[(@class="item"or@class="gift-item") and .//*[contains(text(), "%s")]]';

    /**
     * Selector for gift message on order form.
     *
     * @var string
     */
    protected $giftMessageOrderForm = '#allow-gift-messages-for-order-container';

    /**
     * Selector for "Gift Message" button on order.
     *
     * @var string
     */
    protected $giftMessageItemButton = '//li[@class="gift-item" and .//*[contains(text(), "%s")]]//a';

    /**
     * Selector for "Gift Message" button on item.
     *
     * @var string
     */
    protected $giftMessageOrderButton = '#allow-gift-options-for-order-container a';

    /**
     * Selector for checkbox for 'Add gift options for Individual Items'.
     *
     * @var string
     */
    protected $allowGiftMessagesForItems = '#allow_gift_messages_for_items';

    /**
     * Gift options form css selector.
     *
     * @var string
     */
    protected $giftOptionsForm = '#onepage-checkout-shipping-method-additional-load';

    /**
     * Fill gift message form.
     *
     * @param GiftMessage $giftMessage
     * @param array $products
     * @return void
     */
    public function fillGiftMessage(GiftMessage $giftMessage, array $products = [])
    {
        $this->waitForElementVisible($this->giftOptionsForm);
        $this->addGiftMessage($giftMessage);

        if ($giftMessage->getAllowGiftMessagesForOrder() === 'Yes') {
            $this->fillGiftMessageForOrder($giftMessage);
        }

        if ($giftMessage->getAllowGiftOptionsForItems() === 'Yes') {
            $this->fillGiftGiftOptionsForItems($giftMessage, $products);
        }
    }

    /**
     * @param $giftMessage
     * @throws \Exception
     */
    protected function addGiftMessage($giftMessage)
    {
        $giftMessageData = $giftMessage->getData();
        unset(
            $giftMessageData['sender'],
            $giftMessageData['recipient'],
            $giftMessageData['message'],
            $giftMessageData['items']
        );
        $mapping = $this->dataMapping($giftMessageData);
        $this->_fill($mapping);
    }

    /**
     * Fill gift gift options for items.
     *
     * @param GiftMessage $giftMessage
     * @param array $products
     * @return void
     */
    protected function fillGiftGiftOptionsForItems(GiftMessage $giftMessage, array $products)
    {
        $giftMessageItems = $giftMessage->getItems();
        foreach ($giftMessageItems as $key => $itemGiftMessage) {
            $this->clickGiftMassageItem($products[$key]);
            $this->getGiftMessageItemForm($products[$key])->fill($itemGiftMessage);
        }
    }

    /**
     * Fill gift message for order.
     *
     * @param GiftMessage $giftMessage
     * @return void
     */
    protected function fillGiftMessageForOrder(GiftMessage $giftMessage)
    {
        if ($this->_rootElement->find($this->giftMessageOrderButton)->isVisible()) {
            $this->_rootElement->find($this->giftMessageOrderButton)->click();
        } else {
            $this->_rootElement->find($this->allowGiftMessagesForItems)->click();
        }
        $messageData = $giftMessage->getData();
        unset($messageData['allow_gift_options'], $messageData['allow_gift_messages_for_order']);
        $mapping = $this->getGiftMessageOrderForm()->dataMapping($messageData);
        $this->getGiftMessageOrderForm()->_fill($mapping);
    }

    /**
     * Click gift message item block.
     *
     * @param InjectableFixture $product
     * @return void
     */
    protected function clickGiftMassageItem(InjectableFixture $product)
    {
        $giftMessageItemSelector = sprintf($this->giftMessageItemButton, $product->getName());
        if ($this->_rootElement->find($giftMessageItemSelector, Locator::SELECTOR_XPATH)->isVisible()) {
            $this->_rootElement->find($giftMessageItemSelector, Locator::SELECTOR_XPATH)->click();
        }
    }

    /**
     * Get gift message order form.
     *
     * @return GiftMessageForm
     */
    protected function getGiftMessageOrderForm()
    {
        return $this->blockFactory->create(
            'Mage\GiftMessage\Test\Block\Message\Inline\GiftMessageForm',
            ['element' => $this->_rootElement->find($this->giftMessageOrderForm)]
        );
    }

    /**
     * Get gift message item form.
     *
     * @param InjectableFixture $product
     * @return GiftMessageForm
     */
    protected function getGiftMessageItemForm(InjectableFixture $product)
    {
        $selector = sprintf($this->giftMessageItemForm, $product->getName());
        $this->waitForElementVisible($selector, Locator::SELECTOR_XPATH);

        return $this->blockFactory->create(
            'Mage\GiftMessage\Test\Block\Message\Inline\GiftMessageForm',
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }
}
