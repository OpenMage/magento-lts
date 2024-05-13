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
 * Assert that products are visible in items order block.
 */
class AssertProductsVisibilityInItemsOrderedBlock extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that products are visible in items order block.
     *
     * @param SalesOrderCreateIndex $orderCreatePage
     * @param array $products
     * @return void
     */
    public function processAssert(SalesOrderCreateIndex $orderCreatePage, array $products)
    {
        $productsVisibility = [];
        foreach ($products as $product) {
            $productName = $product->getName();
            if (!$orderCreatePage->getCreateBlock()->getItemsBlock()->getItemProduct($productName)->isVisible()) {
                $productsVisibility[] = "'{$productName}' is not visible in items order block.";
            }
        }
        \PHPUnit_Framework_Assert::assertEmpty($productsVisibility, $productsVisibility);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Products are visible in items order block.';
    }
}
