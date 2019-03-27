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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\CatalogSearch\Test\Constraint;

use Mage\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Mage\CatalogSearch\Test\Page\Adminhtml\CatalogSearchEdit;
use Mage\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that displayed search term data on edit page equals passed from fixture.
 */
class AssertSearchTermForm extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that after save a search term on edit term search page displays:
     *  - correct Search Query field passed from fixture
     *  - correct Synonym For
     *  - correct Redirect URL
     *  - correct Display in Suggested Terms
     *
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchEdit $editPage
     * @param CatalogSearchQuery $searchTerm
     * @return void
     */
    public function processAssert(
        CatalogSearchIndex $indexPage,
        CatalogSearchEdit $editPage,
        CatalogSearchQuery $searchTerm
    ) {
        $indexPage->open()->getGrid()->searchAndOpen(['search_query' => $searchTerm->getQueryText()]);
        \PHPUnit_Framework_Assert::assertEquals(
            $searchTerm->getData(),
            $editPage->getForm()->getData($searchTerm),
            'This form "Search Term" does not match the fixture data.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Search Term form correspond to the data passed from fixture.';
    }
}
