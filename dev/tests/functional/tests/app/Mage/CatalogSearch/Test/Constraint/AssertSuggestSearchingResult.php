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
