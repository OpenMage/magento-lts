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
 * Assert that product can be searched via Quick Search using searchable product attributes.
 */
class AssertProductSearchable extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Filter for search.
     *
     * @var array
     */
    protected $filter = [
        'sku',
        'name'
    ];

    /**
     * Displays an error message.
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Message for passing test.
     *
     * @var string
     */
    protected $successfulMessage;

    /**
     * Displays an error message.
     *
     * @var string
     */
    protected $formatForErrorMessage = 'The product has not been found by %s.';

    /**
     * Message for passing test.
     *
     * @var string
     */
    protected $formatForSuccessfulMessage = 'Product successfully found by %s.';

    /**
     * Assert that product can be searched via Quick Search using searchable product attributes.
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
        foreach ($this->filter as $param) {
            $this->verifySearchResult($catalogSearchResult, $cmsIndex, $product, $param);
        }
    }

    /**
     * Process assert search result.
     *
     * @param CatalogsearchResult $catalogSearchResult
     * @param CmsIndex $cmsIndex
     * @param InjectableFixture $product
     * @param string $param
     * @throws \Exception
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function verifySearchResult(
        CatalogsearchResult $catalogSearchResult,
        CmsIndex $cmsIndex,
        InjectableFixture $product,
        $param
    ) {
        $cmsIndex->open();
        $searchValue = ($product->hasData($param) !== false) ? $product->getData($param) : null;
        if ($searchValue === null) {
            throw new \Exception("Product '$product->getName()' doesn't have '$param' parameter.");
        }
        $param = strtoupper($param);
        $this->errorMessage = sprintf($this->formatForErrorMessage, $param);
        $this->successfulMessage = sprintf($this->formatForSuccessfulMessage, $param);

        $cmsIndex->getSearchBlock()->search($searchValue);

        $quantityAndStockStatus = $product->getStockData();
        $stockStatus = isset($quantityAndStockStatus['is_in_stock'])
            ? $quantityAndStockStatus['is_in_stock']
            : null;

        $isVisible = $catalogSearchResult->getListProductBlock()->isProductVisible($product);
        while (!$isVisible && $catalogSearchResult->getBottomToolbar()->nextPage()) {
            $isVisible = $catalogSearchResult->getListProductBlock()->isProductVisible($product);
        }

        if ($product->getVisibility() === 'Catalog' || $stockStatus === 'Out of Stock') {
            $isVisible = !$isVisible;
            list($this->errorMessage, $this->successfulMessage) = [$this->successfulMessage, $this->errorMessage];
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isVisible,
            $this->errorMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return $this->successfulMessage;
    }
}
