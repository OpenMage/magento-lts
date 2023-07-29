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

use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Create customer step.
 */
class CreateCustomerStep implements TestStepInterface
{
    /**
     * Customer fixture.
     *
     * @var Customer
     */
    protected $customer;

    /**
     * Flag for customer creation.
     *
     * @var bool
     */
    protected $persistCustomer = true;

    /**
     * @constructor
     * @param Customer $customer
     * @param string $customerPersist [optional]
     */
    public function __construct(Customer $customer, $customerPersist = 'yes')
    {
        $this->customer = $customer;
        $this->persistCustomer = $customerPersist;
    }

    /**
     * Create customer.
     *
     * @return array
     */
    public function run()
    {
        if ($this->persistCustomer == 'yes') {
            $this->customer->persist();
        }

        return ['customer' => $this->customer];
    }
}
