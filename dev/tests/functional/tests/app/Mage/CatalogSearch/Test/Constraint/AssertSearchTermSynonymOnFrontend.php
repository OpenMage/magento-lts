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
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that you will be redirected to url from dataset.
 */
class AssertSearchTermSynonymOnFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that you will be redirected to url from dataset.
     *
     * @param CmsIndex $cmsIndex
     * @param Browser $browser
     * @param CatalogSearchQuery $searchTerm
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, Browser $browser, CatalogSearchQuery $searchTerm)
    {
        $cmsIndex->open()->getSearchBlock()->search($searchTerm->getSynonymFor());
        \PHPUnit_Framework_Assert::assertEquals(
            $searchTerm->getRedirect(),
            $browser->getUrl(),
            'Redirect by synonym was not executed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Redirect by synonym executed successfully.';
    }
}
