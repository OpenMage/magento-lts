<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\SalesRule\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\SalesRule\Test\Fixture\SalesRule;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Apply Sales Rule before one page checkout.
 */
class ApplySalesRuleOnFrontendStep implements TestStepInterface
{
    /**
     * Checkout cart page.
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * SalesRule fixture.
     *
     * @var SalesRule
     */
    protected $salesRule;

    /**
     * @constructor
     * @param CheckoutCart $checkoutCart
     * @param SalesRule $salesRule [optional]
     */
    public function __construct(CheckoutCart $checkoutCart, SalesRule $salesRule = null)
    {
        $this->checkoutCart = $checkoutCart;
        $this->salesRule = $salesRule;
    }

    /**
     * Apply sales rule before one page checkout.
     *
     * @return void
     */
    public function run()
    {
        if ($this->salesRule !== null) {
            $this->checkoutCart->getDiscountCodesBlock()->applyCouponCode($this->salesRule->getCouponCode());
        }
    }
}
