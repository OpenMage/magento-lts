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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
            strpos($cmsIndex->getCompareBlock()->getCompareLinkUrl(), $this->compareProductUrl) !== false,
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
