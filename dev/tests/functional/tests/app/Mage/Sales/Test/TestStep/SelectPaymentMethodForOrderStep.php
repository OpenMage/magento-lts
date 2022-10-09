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

use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreateIndex;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Fill Payment Data Step
 */
class SelectPaymentMethodForOrderStep implements TestStepInterface
{
    /**
     * Sales order create index page.
     *
     * @var SalesOrderCreateIndex
     */
    protected $salesOrderCreateIndex;

    /**
     * Payment data.
     *
     * @var array
     */
    protected $payment;

    /**
     * @constructor
     * @param SalesOrderCreateIndex $salesOrderCreateIndex
     * @param array $payment
     */
    public function __construct(SalesOrderCreateIndex $salesOrderCreateIndex, array $payment)
    {
        $this->salesOrderCreateIndex = $salesOrderCreateIndex;
        $this->payment = $payment;
    }

    /**
     * Fill Payment data.
     *
     * @return void
     */
    public function run()
    {
        $this->salesOrderCreateIndex->getCreateBlock()->getPaymentBlock()->selectPaymentMethod($this->payment);
    }
}
