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

use Mage\Cms\Test\Fixture\CmsPage;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Cms\Test\Page\Adminhtml\CmsPageIndex;

/**
 * Assert that CMS page present in grid and can be found by title.
 */
class AssertCmsPageInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that cms page is present in pages grid.
     *
     * @param CmsPage $cms
     * @param CmsPageIndex $cmsPageIndex
     * @return void
     */
    public function processAssert(CmsPage $cms, CmsPageIndex $cmsPageIndex)
    {
        $cmsPageIndex->open();
        $cmsTitle = $cms->getTitle();
        \PHPUnit_Framework_Assert::assertTrue(
            $cmsPageIndex->getCmsPageGridBlock()->isRowVisible(['title' => $cmsTitle]),
            "Cms page '$cmsTitle' is not present in pages grid."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Cms page is present in pages grid.';
    }
}
