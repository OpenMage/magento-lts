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

namespace Mage\Cms\Test\Constraint;

use Mage\Cms\Test\Fixture\CmsPage;
use Mage\Cms\Test\Page\Adminhtml\CmsPageIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that CMS page cann't be found in grid via:
 * - Page title type
 * - Url Key
 * - Status
 */
class AssertCmsPageNotInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that CMS page cann't be found in grid via:
     * - Page title type
     * - Url Key
     * - Status
     *
     * @param CmsPageIndex $cmsIndex
     * @param CmsPage $cmsPage
     * @return void
     */
    public function processAssert(CmsPageIndex $cmsIndex, CmsPage $cmsPage)
    {
        $cmsIndex->open();
        $filter = [
            'title' => $cmsPage->getTitle(),
            'identifier' => $cmsPage->getIdentifier(),
            'is_active' => $cmsPage->getIsActive()
        ];
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsIndex->getCmsPageGridBlock()->isRowVisible($filter),
            "Cms page {$cmsPage->getTitle()} is present in pages grid."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Cms page is not present in pages grid.';
    }
}
