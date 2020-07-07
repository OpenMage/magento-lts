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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\CatalogSearch\Test\Page\CatalogsearchResult;

/**
 * Assert that product cannot be found via Quick Search using searchable product attributes.
 */
class AssertProductNotSearchableBySku extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product cannot be found via Quick Search using searchable product attributes.
     *
     * @param CatalogsearchResult $catalogSearchResult
     * @param CmsIndex $cmsIndex
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(
        CatalogsearchResult $catalogSearchResult,
        CmsIndex $cmsIndex,
        InjectableFixture $product
    ) {
        $cmsIndex->open();
        $cmsIndex->getSearchBlock()->search($product->getSku());
        \PHPUnit_Framework_Assert::assertFalse(
            $catalogSearchResult->getListProductBlock()->isProductVisible($product),
            'Product was found by SKU.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Product is not searchable by SKU.";
    }
}
