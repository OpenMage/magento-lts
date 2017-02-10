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

namespace Mage\Cms\Test\Constraint;

use Mage\Cms\Test\Page\Adminhtml\CmsPageIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that success delete message is displayed after remove Cms page.
 */
class AssertCmsPageDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success CMS page delete message.
     */
    const SUCCESS_DELETE_MESSAGE = 'The page has been deleted.';

    /**
     * Assert that success delete message is displayed after remove Cms page.
     *
     * @param CmsPageIndex $cmsIndex
     * @return void
     */
    public function processAssert(CmsPageIndex $cmsIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $cmsIndex->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Cms page success delete message is present.';
    }
}
