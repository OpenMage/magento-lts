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

use Mage\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that after delete a category successful delete message appears.
 */
class AssertCategorySuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Message that displayed after delete category.
     */
    const SUCCESS_DELETE_MESSAGE = 'The category has been deleted.';

    /**
     * Assert that after delete a category successful delete message appears.
     *
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @return void
     */
    public function processAssert(CatalogCategoryIndex $catalogCategoryIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $catalogCategoryIndex->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Return string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Category success delete message is displayed.';
    }
}
