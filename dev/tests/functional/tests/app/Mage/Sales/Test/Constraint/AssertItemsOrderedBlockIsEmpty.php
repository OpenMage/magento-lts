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

use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreateIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that items ordered block is empty.
 */
class AssertItemsOrderedBlockIsEmpty extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that items ordered block is empty.
     *
     * @param SalesOrderCreateIndex $orderCreatePage
     * @return void
     */
    public function processAssert(SalesOrderCreateIndex $orderCreatePage)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $orderCreatePage->getCreateBlock()->getItemsBlock()->isEmptyBlockVisible(),
            "Items ordered block is not empty"
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Items ordered block is empty';
    }
}
