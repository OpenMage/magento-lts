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

namespace Mage\Authorizenet\Test\TestCase;

use Mage\Paypal\Test\TestCase\CreateOrderWithOnlinePaymentsMethodsWithoutIFrameTest;

/**
 * Preconditions:
 * 1. Create product.
 * 2. Apply configuration for test.
 *
 * Steps:
 * 1. Go to Frontend.
 * 2. Add products to the cart.
 * 3. Click the 'Proceed to Checkout' button.
 * 4. Select checkout method according to dataset.
 * 5. Fill billing information and select the 'Ship to this address' option.
 * 6. Select shipping method according to dataset.
 * 7. Select payment method according to dataset.
 * 8. Fill credit card and place order.
 * 9. Perform assertions.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MPERF-7199
 */
class CreateOrderWithAuthorizenetDirectpostMethodTest extends CreateOrderWithOnlinePaymentsMethodsWithoutIFrameTest
{
    /* tags */
    const TEST_TYPE = '3rd_party_test';
    /* end tags */
}
