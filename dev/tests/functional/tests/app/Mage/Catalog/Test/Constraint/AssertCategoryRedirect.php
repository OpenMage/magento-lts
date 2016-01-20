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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;

/**
 * Assert that old Category URL lead to appropriate Category in frontend.
 */
class AssertCategoryRedirect extends AbstractConstraint
{
    /* tags */
    const SEVERITY = '';
    /* end tags */

    /**
     * Assert that old category URL lead to appropriate Category in frontend.
     *
     * @param CatalogCategory $category
     * @param Browser $browser
     * @param CatalogCategory $initialCategory
     * @param CatalogCategoryView $catalogCategoryView
     * @return void
     */
    public function processAssert(
        CatalogCategory $category,
        Browser $browser,
        CatalogCategory $initialCategory,
        CatalogCategoryView $catalogCategoryView
    ) {
        $browser->open(str_replace('index', 'cron', $_ENV['app_frontend_url']));
        $browser->open($_ENV['app_frontend_url'] . $initialCategory->getUrlKey() . '.html');
        \PHPUnit_Framework_Assert::assertEquals(
            $catalogCategoryView->getTitleBlock()->getTitle(),
            strtoupper($category->getName()),
            'Old category URL does not lead to appropriate Category in frontend.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Old category URL lead to appropriate Category in frontend.';
    }
}
