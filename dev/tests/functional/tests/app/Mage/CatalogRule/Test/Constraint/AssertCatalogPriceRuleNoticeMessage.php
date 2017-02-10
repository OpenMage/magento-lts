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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\CatalogRule\Test\Constraint;

use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that catalog price rule notice message present on Catalog Rule index page.
 */
class AssertCatalogPriceRuleNoticeMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Text value to be checked.
     */
    const NOTICE_MESSAGE_RULES = 'There are rules that have been changed but were not applied.';

    /**
     * Text value to be checked.
     */
    const NOTICE_MESSAGE_APPLY = ' Please, click Apply Rules in order to see immediate effect in the catalog.';

    /**
     * Assert that message "There are rules that have been changed but were not applied..."
     * is present on page after Save (without applying Rule)
     * or after Edit (without applying Rule) action on the Catalog Price Rules page.
     *
     * @param CatalogRuleIndex $pageCatalogRuleIndex
     * @return void
     */
    public function processAssert(CatalogRuleIndex $pageCatalogRuleIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::NOTICE_MESSAGE_RULES . self::NOTICE_MESSAGE_APPLY,
            $pageCatalogRuleIndex->getMessagesBlock()->getNoticeMessages()
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that notice message is displayed';
    }
}
