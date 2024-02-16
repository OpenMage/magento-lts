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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
