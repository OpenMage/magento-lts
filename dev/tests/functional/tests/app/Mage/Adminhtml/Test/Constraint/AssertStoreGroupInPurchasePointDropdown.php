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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
