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

namespace Mage\SalesRule\Test\TestStep;

use Mage\SalesRule\Test\Fixture\SalesRule;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreateIndex;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Apply sales rule during backend order creation.
 */
class ApplySalesRuleOnBackendStep implements TestStepInterface
{
    /**
     * SalesRule fixture.
     *
     * @var SalesRule
     */
    protected $salesRule;

    /**
     * Order create backend page.
     *
     * @var SalesOrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * @constructor
     * @param SalesOrderCreateIndex $orderCreateIndex
     * @param SalesRule $salesRule [optional]
     */
    public function __construct(SalesOrderCreateIndex $orderCreateIndex,SalesRule $salesRule = null)
    {
        $this->salesRule = $salesRule;
        $this->orderCreateIndex = $orderCreateIndex;
    }

    /**
     * Apply sales rule during backend order creation.
     *
     * @return void
     */
    public function run()
    {
        if ($this->salesRule !== null) {
            if ($this->salesRule->hasData('coupon_code')) {
                $this->orderCreateIndex->getCreateBlock()->getCouponsBlock()->applyCouponsCode($this->salesRule);
            }
        }
    }
}
