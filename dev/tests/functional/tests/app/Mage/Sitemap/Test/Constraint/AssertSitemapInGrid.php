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

namespace Mage\Sitemap\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Sitemap\Test\Page\Adminhtml\SitemapIndex;
use Mage\Sitemap\Test\Fixture\Sitemap;

/**
 * Assert that sitemap is present in sitemap grid.
 */
class AssertSitemapInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that sitemap is present in sitemap grid.
     *
     * @param Sitemap $sitemap
     * @param SitemapIndex $sitemapIndex
     * @return void
     */
    public function processAssert(Sitemap $sitemap, SitemapIndex $sitemapIndex)
    {
        $sitemapIndex->open()->getSitemapGrid()->sortGridByField('sitemap_id');
        $filter = [
            'sitemap_filename' => $sitemap->getSitemapFilename(),
            'sitemap_path' => $sitemap->getSitemapPath(),
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $sitemapIndex->getSitemapGrid()->isRowVisible($filter),
            'Sitemap is absent in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sitemap is present in grid.';
    }
}
