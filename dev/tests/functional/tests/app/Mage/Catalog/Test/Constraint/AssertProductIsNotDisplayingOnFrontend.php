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

use Magento\Mtf\Client\Browser;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\CatalogSearch\Test\Page\CatalogsearchResult;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that product is not displayed on frontend.
 */
class AssertProductIsNotDisplayingOnFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Catalog search result page.
     *
     * @var CatalogsearchResult
     */
    protected $catalogSearchResult;

    /**
     * Fixture category.
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * Catalog category view page.
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Assert that product is not displayed on frontend.
     *
     * @param InjectableFixture $product
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param CmsIndex $cmsIndex
     * @param CatalogsearchResult $catalogSearchResult
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogCategory|null $category
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        Browser $browser,
        CatalogProductView $catalogProductView,
        CmsIndex $cmsIndex,
        CatalogsearchResult $catalogSearchResult,
        CatalogCategoryView $catalogCategoryView,
        CatalogCategory $category = null
    ) {
        $this->browser = $browser;
        $this->catalogProductView = $catalogProductView;
        $this->cmsIndex = $cmsIndex;
        $this->catalogSearchResult = $catalogSearchResult;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->category = $category;
        $products = is_array($product) ? $product : [$product];
        foreach ($products as $product) {
            $errors = $this->isNotDisplayingOnFrontendAssert($product);
            \PHPUnit_Framework_Assert::assertEmpty(
                $errors,
                "In the process of checking product availability on the frontend, found the following errors:\n"
                . implode("\n", $errors)
            );
        }
    }

    /**
     * Verify product displaying on frontend.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function isNotDisplayingOnFrontendAssert(InjectableFixture $product)
    {
        $errors = [];
        //Check that product is not available by url
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        if ($this->catalogProductView->getViewBlock()->isVisible()) {
            $errors[] = '- product view block is visible in product page.';
        }
        //Check that product can't be found
        $this->cmsIndex->open()->getSearchBlock()->search($product->getSku());
        if ($this->catalogSearchResult->getListProductBlock()->isProductVisible($product)) {
            $errors[] = '- successful product search.';
        }
        //Check that product is not available in category page
        $categoryName = $product->hasData('category_ids') ? $product->getCategoryIds()[0] : $this->category->getName();
        $this->cmsIndex->open()->getTopmenu()->selectCategory($categoryName);
        $isProductVisible = $this->catalogCategoryView->getListProductBlock()->isProductVisible($product);
        $bottomToolBar = $this->catalogCategoryView->getBottomToolbar();
        while (!$isProductVisible && $bottomToolBar->nextPage()) {
            $isProductVisible = $this->catalogCategoryView->getListProductBlock()
                ->isProductVisible($product);
        }
        if ($isProductVisible) {
            $errors[] = "- product with name '{$product->getName()}' is found in this category.";
        }

        return $errors;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is not displayed on frontend.';
    }
}
