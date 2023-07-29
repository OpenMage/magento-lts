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

namespace Mage\Customer\Test\TestStep;

use Mage\Customer\Test\Page\Adminhtml\CustomerEdit;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Create order from customer page on backend.
 */
class CreateOrderFromCustomerAccountStep implements TestStepInterface
{
    /**
     * Customer edit page.
     *
     * @var CustomerEdit
     */
    protected $customerIndexEdit;

    /**
     * @constructor
     * @param CustomerEdit $customerIndexEdit
     */
    public function __construct(CustomerEdit $customerIndexEdit)
    {
        $this->customerIndexEdit = $customerIndexEdit;
    }

    /**
     * Create new order from customer.
     *
     * @return void
     */
    public function run()
    {
        $this->customerIndexEdit->getPageActionsBlock()->createOrder();
    }
}
