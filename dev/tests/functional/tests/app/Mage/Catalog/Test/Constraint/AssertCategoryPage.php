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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that displayed category data on category page equals to passed from fixture.
 */
class AssertCategoryPage extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that displayed category data on category page equals to passed from fixture.
     *
     * @param CatalogCategory $category
     * @param CatalogCategory $initialCategory
     * @param FixtureFactory $fixtureFactory
     * @param CatalogCategoryView $categoryView
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogCategory $category,
        CatalogCategory $initialCategory,
        FixtureFactory $fixtureFactory,
        CatalogCategoryView $categoryView,
        Browser $browser
    ) {
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            [
                'dataSet' => 'default',
                'data' => [
                    'category_ids' => [
                        'category' => $initialCategory
                    ]
                ]
            ]
        );
        $categoryData = array_merge($initialCategory->getData(), $category->getData());
        $product->persist();
        $url = $_ENV['app_frontend_url'] . strtolower($category->getUrlKey()) . '.html';
        $browser->open($url);
        \PHPUnit_Framework_Assert::assertEquals(
            $url,
            $browser->getUrl(),
            'Wrong page URL.'
        );

        if (isset($categoryData['name'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                strtoupper($categoryData['name']),
                $categoryView->getTitleBlock()->getTitle(),
                'Wrong page title.'
            );
        }

        if (isset($categoryData['description'])) {
            \PHPUnit_Framework_Assert::assertEquals(
                $categoryData['description'],
                $categoryView->getViewBlock()->getDescription(),
                'Wrong category description.'
            );
        }

        if (isset($categoryData['default_sort_by'])) {
            $sortBy = strtolower($categoryData['default_sort_by']);
            $sortType = $categoryView->getTopToolbar()->getSelectSortType();
            \PHPUnit_Framework_Assert::assertEquals(
                $sortBy,
                $sortType,
                'Wrong sorting type.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Category data on category page equals to passed from fixture.';
    }
}
