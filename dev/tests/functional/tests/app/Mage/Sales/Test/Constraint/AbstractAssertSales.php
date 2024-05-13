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

namespace Mage\Sales\Test\Constraint;

use Mage\Sales\Test\Fixture\Order;
use Magento\Mtf\Constraint\AbstractAssertForm;

/**
 * Abstract assert for sales asserts.
 */
abstract class AbstractAssertSales extends AbstractAssertForm
{
    /**
     * Entity type.
     *
     * @var string
     */
    protected $entityType;

    /**
     * Error message.
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Verify data for assert.
     *
     * @var array
     */
    protected $verifyData;

    /**
     * Order fixture.
     *
     * @var Order|null
     */
    protected $order;

    /**
     * Order id.
     *
     * @var string|null
     */
    protected $orderId;
}
