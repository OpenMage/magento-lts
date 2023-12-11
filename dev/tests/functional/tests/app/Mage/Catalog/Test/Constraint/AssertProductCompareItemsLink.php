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
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that link "Compare Products..." is present on top menu of page.
 */
class AssertProductCompareItemsLink extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Compare product url.
     *
     * @var string
     */
    protected $compareProductUrl = '/catalog/product_compare/';

    /**
     * Assert that link "Compare Products..." is present on top menu of page.
     *
     * @param CmsIndex $cmsIndex
     * @param array $products
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, array $products)
    {
        $cmsIndex->open();
        \PHPUnit_Framework_Assert::assertEquals(
            count($products),
            $cmsIndex->getCompareBlock()->getQtyInCompareList(),
            'Qty is not correct in "Compare Products" link.'
        );

        \PHPUnit_Framework_Assert::assertTrue(
            str_contains($cmsIndex->getCompareBlock()->getCompareLinkUrl(), $this->compareProductUrl),
            'Compare product link isn\'t lead to Compare Product Page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return '"Compare Products..." link on top menu of page is correct.';
    }
}
