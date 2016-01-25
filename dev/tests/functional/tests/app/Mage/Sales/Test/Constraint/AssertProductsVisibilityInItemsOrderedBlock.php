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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
