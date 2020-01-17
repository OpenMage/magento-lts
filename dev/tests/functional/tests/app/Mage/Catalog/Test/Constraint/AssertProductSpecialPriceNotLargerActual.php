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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductNew;

/**
 * Assert that special price can't be larger, than actual.
 */
class AssertProductSpecialPriceNotLargerActual extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Text value to be checked.
     */
    const ERROR_MESSAGE = 'The Special Price is active only when lower than the Actual Price.';

    /**
     * Assert that special price can't be larger, than actual.
     *
     * @param CatalogProductNew $productPage
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(CatalogProductNew $productPage, InjectableFixture $product)
    {
        $errorMessages = $productPage->getProductForm()->getRequireNoticeAttributes($product);
        \PHPUnit_Framework_Assert::assertEquals(self::ERROR_MESSAGE, $errorMessages['prices']['specialprice']);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Notice message is displayed in price tab on new product page.";
    }
}
