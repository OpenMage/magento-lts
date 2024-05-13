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

namespace Mage\CatalogSearch\Test\Constraint;

use Mage\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that after input some text(e.g. product name) into search field, drop-down window is appeared.
 */
class AssertSuggestSearchingResult extends AbstractConstraint
{
    /**
     * Check that after input some text(e.g. product name) into search field, drop-down window is appeared.
     * Window contains requested entity and number of quantity.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogSearchQuery $catalogSearch
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, CatalogSearchQuery $catalogSearch)
    {
        $cmsIndex->open();
        $searchBlock = $cmsIndex->getSearchBlock();
        $queryText = $catalogSearch->getQueryText();
        $searchBlock->fillSearch($queryText);

        $isVisible = $catalogSearch->hasData('num_results')
            ? $searchBlock->isSuggestSearchVisible($queryText, $catalogSearch->getNumResults())
            : $searchBlock->isSuggestSearchVisible($queryText);

        \PHPUnit_Framework_Assert::assertTrue($isVisible, 'Block "Suggest Search" when searching was not found.');
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Asserts that window contains requested entity and quantity.';
    }
}
