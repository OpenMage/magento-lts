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

namespace Mage\Sales\Test\TestStep;

use Mage\Customer\Test\Fixture\Address;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreateIndex;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Fill Billing address data.
 */
class FillBillingAddressStep implements TestStepInterface
{
    /**
     * Sales order create index page.
     *
     * @var SalesOrderCreateIndex
     */
    protected $salesOrderCreateIndex;

    /**
     * Address fixture.
     *
     * @var Address
     */
    protected $billingAddress;

    /**
     * @constructor
     * @param SalesOrderCreateIndex $salesOrderCreateIndex
     * @param Address $billingAddress
     */
    public function __construct(SalesOrderCreateIndex $salesOrderCreateIndex, Address $billingAddress)
    {
        $this->salesOrderCreateIndex = $salesOrderCreateIndex;
        $this->billingAddress = $billingAddress;
    }

    /**
     * Fill Billing address.
     *
     * @return Address
     */
    public function run()
    {
        $this->salesOrderCreateIndex->getCreateBlock()->getBillingAddressForm()->fill($this->billingAddress);

        return ['billingAddress' => $this->billingAddress];
    }
}
