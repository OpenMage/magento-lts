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

namespace Mage\Adminhtml\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Adminhtml\Test\Fixture\Store;
use Mage\Cms\Test\Page\CmsIndex;

/**
 * Assert that created store view is not available on frontend.
 */
class AssertStoreNotOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created store view is not available on frontend.
     *
     * @param Store $store
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(Store $store, CmsIndex $cmsIndex)
    {
        $cmsIndex->open();
        $footerBlock = $cmsIndex->getFooterBlock();
        $headerBlock = $cmsIndex->getHeaderBlock();
        if ($footerBlock->isStoreGroupSwitcherVisible() && $footerBlock->isStoreGroupVisible($store)) {
            $footerBlock->selectStoreGroup($store);
        }

        $isStoreViewVisible = $headerBlock->isStoreViewDropdownVisible()
            ? $headerBlock->isStoreViewVisible($store)
            : false; // if only one store view is assigned to store group

        \PHPUnit_Framework_Assert::assertFalse(
            $isStoreViewVisible,
            "Store view '{$store->getName()}' is visible in dropdown on CmsIndex page."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Store view is not visible in dropdown on CmsIndex page.';
    }
}
