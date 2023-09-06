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

namespace Mage\Adminhtml\Test\Constraint;

use Mage\Adminhtml\Test\Fixture\StoreGroup;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that created Store Group can be found in "Purchase Point" dropdown on SalesOrderIndex page.
 */
class AssertStoreGroupInPurchasePointDropdown extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created Store Group can be found in "Purchase Point" dropdown on SalesOrderIndex page.
     *
     * @param SalesOrderIndex $salesOrderIndex
     * @param array $storeGroups
     * @return void
     */
    public function processAssert(SalesOrderIndex $salesOrderIndex, array $storeGroups)
    {
        $salesOrderIndex->open();
        foreach ($storeGroups as $storeGroup) {
            /** @var StoreGroup $storeGroup */
            $storeGroupName = $storeGroup->getName();
            \PHPUnit_Framework_Assert::assertTrue(
                $salesOrderIndex->getSalesOrderGrid()->isRowVisible(['purchased_from' => $storeGroupName]),
                "Store group '$storeGroupName' is not present in grid filter."
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Store Group can be found in "Purchase Point" dropdown on SalesOrderIndex page.';
    }
}
