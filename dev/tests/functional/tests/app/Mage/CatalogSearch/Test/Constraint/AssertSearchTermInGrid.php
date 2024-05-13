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
