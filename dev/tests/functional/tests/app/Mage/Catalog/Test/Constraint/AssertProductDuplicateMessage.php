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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Checking the output message successful product duplication.
 */
class AssertProductDuplicateMessage extends AbstractConstraint
{
    /**
     * Text value to be checked.
     */
    const DUPLICATE_MESSAGE = 'The product has been duplicated.';

    /**
     * Checking the output message successful product duplication.
     *
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(CatalogProductEdit $productPage)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::DUPLICATE_MESSAGE,
            $productPage->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product duplicated message is present.';
    }
}
