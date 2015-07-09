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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\GiftMessage\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutOnepage;
use Mage\GiftMessage\Test\Fixture\GiftMessage;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Add gift message step to order or item.
 */
class AddGiftMessageStep implements TestStepInterface
{
    /**
     * Onepage checkout page.
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Gift message fixture.
     *
     * @var GiftMessage
     */
    protected $giftMessage;

    /**
     * Array with products.
     *
     * @var array
     */
    protected $products;

    /**
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param GiftMessage $giftMessage
     * @param array $products [optional]
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, GiftMessage $giftMessage, array $products = [])
    {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->giftMessage = $giftMessage;
        $this->products = $products;
    }

    /**
     * Add gift message to order.
     *
     * @return array
     */
    public function run()
    {
        $this->checkoutOnepage->getGiftMessagesBlock()->fillGiftMessage($this->giftMessage, $this->products);

        return ['giftMessage' => $this->giftMessage];
    }
}
