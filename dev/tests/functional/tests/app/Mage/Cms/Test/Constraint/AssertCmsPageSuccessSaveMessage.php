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

namespace Mage\Cms\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Cms\Test\Page\Adminhtml\CmsPageIndex;

/**
 * Assert that after save a CMS page successful message appears.
 */
class AssertCmsPageSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success CMS page save message.
     */
    const SUCCESS_SAVE_MESSAGE = 'The page has been saved.';

    /**
     * Assert that after save a CMS page successful message appears.
     *
     * @param CmsPageIndex $cmsPageIndex
     * @return void
     */
    public function processAssert(CmsPageIndex $cmsPageIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $cmsPageIndex->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message is displayed after save a CMS page.';
    }
}
