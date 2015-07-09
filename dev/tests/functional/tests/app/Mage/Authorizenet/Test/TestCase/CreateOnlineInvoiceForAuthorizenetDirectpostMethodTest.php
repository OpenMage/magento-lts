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

namespace Mage\Authorizenet\Test\TestCase;

use Mage\Paypal\Test\TestCase\CreateOnlineInvoiceForOnlinePaymentsMethodsWithIFrameTest;

/**
 * Preconditions:
 * 1. Create product.
 * 2. Apply configuration for test.
 *
 * Steps:
 * 1. Go to Frontend.
 * 2. Add products to the cart.
 * 3. Click the 'Proceed to Checkout' button.
 * 4. Select checkout method according to dataSet.
 * 5. Fill billing information and select the 'Ship to this address' option.
 * 6. Select shipping method according to dataSet.
 * 7. Select payment method according to dataSet.
 * 8. Place order.
 * 9. Go to Sales > Orders.
 * 10. Select created order in the grid and open it.
 * 11. Click 'Invoice' button.
 * 12. Fill data according to dataSet.
 * 13. Click 'Submit Invoice' button.
 * 14. Perform assertions.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MPERF-7251
 */
class CreateOnlineInvoiceForAuthorizenetDirectpostMethodTest
    extends CreateOnlineInvoiceForOnlinePaymentsMethodsWithIFrameTest
{
    /* tags */
    const TEST_TYPE = '3rd_party_test';
    /* end tags */
}
