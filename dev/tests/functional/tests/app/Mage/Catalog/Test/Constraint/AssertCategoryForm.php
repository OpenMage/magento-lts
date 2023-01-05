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

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Magento\Mtf\Constraint\AbstractAssertForm;

/**
 * Check that displayed category data on edit page(backend) equals passed from fixture.
 */
class AssertCategoryForm extends AbstractAssertForm
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    protected $skippedFields = [
        'parent_id',
        'id',
        'path'
    ];

    /**
     * Assert that displayed category data on edit page(backend) equals passed from fixture.
     *
     * @param CatalogCategory $category
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @return void
     */
    public function processAssert(CatalogCategory $category, CatalogCategoryIndex $catalogCategoryIndex)
    {
        $data = $category->getData();
        $catalogCategoryIndex->open();
        $catalogCategoryIndex->getTreeCategories()->selectCategory($category);
        $dataForm = $catalogCategoryIndex->getCategoryForm()->getDataCategory($category);
        $error = $this->verifyData($data, $dataForm);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed category data on edit page(backend) equals to passed from fixture.';
    }
}
