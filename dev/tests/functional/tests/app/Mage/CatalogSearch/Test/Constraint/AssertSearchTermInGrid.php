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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\CatalogSearch\Test\Constraint;

use Mage\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Mage\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that term search present in grid.
 */
class AssertSearchTermInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that after save a term search on edit term search page displays:
     *  - correct Search Query field passed from fixture
     *  - correct Synonym
     *  - correct Redirect URL
     *  - correct Suggested Terms
     *
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchQuery $searchTerm
     * @return void
     */
    public function processAssert(CatalogSearchIndex $indexPage, CatalogSearchQuery $searchTerm)
    {
        $indexPage->open();
        $filters = [
            'search_query' => $searchTerm->getQueryText(),
            'synonym_for' => $searchTerm->getSynonymFor(),
            'redirect' => $searchTerm->getRedirect(),
            'display_in_terms' => $searchTerm->getDisplayInTerms(),
        ];

        $indexPage->getGrid()->search($filters);
        \PHPUnit_Framework_Assert::assertTrue(
            $indexPage->getGrid()->isRowVisible($filters, false),
            'Row terms according to the filters is not found.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Row term according to the filters is present.';
    }
}
