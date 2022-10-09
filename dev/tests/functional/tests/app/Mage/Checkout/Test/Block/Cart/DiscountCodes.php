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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block\Cart;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;

/**
 * Discount codes block.
 */
class DiscountCodes extends Form
{
    /**
     * Discount code input css selector.
     *
     * @var string
     */
    protected $couponCode = '#coupon_code';

    /**
     * Apply button selector.
     *
     * @var string
     */
    protected $applyButton = '.discount-form .button2';

    /**
     * Enter discount code and click apply button.
     *
     * @param string $code
     * @return void
     */
    public function applyCouponCode($code)
    {
        $this->_rootElement->find($this->couponCode, Locator::SELECTOR_CSS)->setValue($code);
        $this->_rootElement->find($this->applyButton, Locator::SELECTOR_CSS)->click();
    }
}
