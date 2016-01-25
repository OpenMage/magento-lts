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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\GiftMessage\Test\Block\Message\Order\Items;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Gift message block for order's items on order view page.
 */
class View extends \Mage\GiftMessage\Test\Block\Message\Order\View
{
    /**
     * Selector for "Gift Message" button.
     *
     * @var string
     */
    protected $giftMessageButton = "//tbody[.//*[contains(text(),'%s')]]//a[contains(@id,'gift-message')]";

    /**
     * Selector for "Gift Message".
     *
     * @var string
     */
    protected $giftMessageForItem = "//tr[.//*[contains(text(),'%s')]]/following-sibling::tr//*[@class='gift-message']";

    /**
     * Get gift message for item.
     *
     * @param InjectableFixture $giftItem
     * @return array
     */
    public function getItemGiftMessage(InjectableFixture $giftItem)
    {
        $giftMessageElement = $this->getGiftMessageElement($giftItem);
        return $this->getGiftMessage($giftMessageElement);
    }

    /**
     * Get gift message element.
     *
     * @param InjectableFixture $giftItem
     * @return Element
     */
    protected function getGiftMessageElement(InjectableFixture $giftItem)
    {
        $itemName = $giftItem->getName();
        $this->showGiftMessageElement($itemName);

        return $this->_rootElement->find(sprintf($this->giftMessageForItem, $itemName), Locator::SELECTOR_XPATH);
    }

    /**
     * Click "Gift Message" for special item.
     *
     * @param string $itemName
     * @return void
     */
    protected function showGiftMessageElement($itemName)
    {
        $this->_rootElement->find(sprintf($this->giftMessageButton, $itemName), Locator::SELECTOR_XPATH)->click();
    }
}
