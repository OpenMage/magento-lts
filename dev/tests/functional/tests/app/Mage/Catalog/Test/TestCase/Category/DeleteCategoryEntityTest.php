<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestCase\Category;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Create category.
 *
 * Steps:
 * 1. Login as admin.
 * 2. Navigate to the Catalog -> Categories -> Manage Categories.
 * 3. Open category created in preconditions.
 * 4. Click 'Delete' button.
 * 5. Perform all assertions.
 *
 * @group Category_Management_(MX)
 * @ZephyrId MPERF-7330
 */
class DeleteCategoryEntityTest extends Injectable
{
    /**
     * Catalog category index page.
     *
     * @var CatalogCategoryIndex
     */
    protected $catalogCategoryIndex;

    /**
     * Injection page.
     *
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @return void
     */
    public function __inject(CatalogCategoryIndex $catalogCategoryIndex)
    {
        $this->catalogCategoryIndex = $catalogCategoryIndex;
    }

    /**
     * Delete category.
     *
     * @param CatalogCategory $category
     * @return void
     */
    public function test(CatalogCategory $category)
    {
        // Preconditions:
        $category->persist();

        // Steps:
        $this->catalogCategoryIndex->open();
        $this->catalogCategoryIndex->getTreeCategories()->selectCategory($category);
        $this->catalogCategoryIndex->getFormPageActions()->deleteAndAcceptAlert();
    }
}
