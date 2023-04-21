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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that product success add message is present on CatalogProductView page.
 */
class AssertProductCompareSuccessAddMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Success message.
     */
    const SUCCESS_MESSAGE = 'The product %s has been added to comparison list.';

    /**
     * Assert that product success add message is present on CatalogProductView page.
     *
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $product
     * @return bool
     */
    public function processAssert(CatalogProductView $catalogProductView, InjectableFixture $product)
    {
        $successMessage = sprintf(self::SUCCESS_MESSAGE, $product->getName());
        $actualMessage = $catalogProductView->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals($successMessage, $actualMessage);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Product has been added to compare products list.";
    }
}
