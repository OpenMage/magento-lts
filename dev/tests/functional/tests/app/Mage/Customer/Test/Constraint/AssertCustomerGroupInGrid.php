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

namespace Mage\Customer\Test\Constraint;

use Mage\Customer\Test\Fixture\CustomerGroup;
use Mage\Customer\Test\Page\Adminhtml\CustomerGroupIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that customer group is present in customer groups grid.
 */
class AssertCustomerGroupInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that customer group is present in customer groups grid.
     *
     * @param CustomerGroup $customerGroup
     * @param CustomerGroupIndex $customerGroupIndex
     * @return void
     */
    public function processAssert(CustomerGroup $customerGroup, CustomerGroupIndex $customerGroupIndex)
    {
        $customerGroupIndex->open();
        $customerGroupCode = $customerGroup->getCustomerGroupCode();
        \PHPUnit_Framework_Assert::assertTrue(
            $customerGroupIndex->getCustomerGroupGrid()->isRowVisible(['code' => $customerGroupCode]),
            "Group '{$customerGroupCode}' is absent in customer groups grid."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group is present in customer groups grid.';
    }
}
