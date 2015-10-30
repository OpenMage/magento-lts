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

namespace Mage\Catalog\Test\TestCase\Category;

use Magento\Mtf\TestCase\Injectable;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;

/**
 * Steps:
 * 1. Login as admin.
 * 2. Navigate to the Catalog->Categories->Manage Categories.
 * 3. Select parent category.
 * 4. Click on 'Add Category' button.
 * 5. Fill out all data according to data set.
 * 6. Save category.
 * 7. Perform all assertions.
 *
 * @group Category_Management_(MX)
 * @ZephyrId MPERF-6712
 */
class CreateCategoryEntityTest extends Injectable
{
    /**
     * Catalog category index page.
     *
     * @var CatalogCategoryIndex
     */
    protected $catalogCategoryIndex;

    /**
     * Inject page.
     *
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @return void
     */
    public function __inject(CatalogCategoryIndex $catalogCategoryIndex)
    {
        $this->catalogCategoryIndex = $catalogCategoryIndex;
    }

    /**
     * Create category.
     *
     * @param CatalogCategory $category
     * @param string $addCategory
     * @return void
     */
    public function test(CatalogCategory $category, $addCategory)
    {
        // Steps
        $this->catalogCategoryIndex->open();
        $this->catalogCategoryIndex->getTreeCategories()->selectCategory($category, false);
        $this->catalogCategoryIndex->getTreeCategories()->$addCategory();
        $this->catalogCategoryIndex->getCategoryForm()->fill($category);
        $this->catalogCategoryIndex->getFormPageActions()->save();
    }
}
