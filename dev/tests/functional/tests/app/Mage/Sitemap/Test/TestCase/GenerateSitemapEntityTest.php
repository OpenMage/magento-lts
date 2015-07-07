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

namespace Mage\Sitemap\Test\TestCase;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Cms\Test\Fixture\CmsPage;
use Mage\Sitemap\Test\Fixture\Sitemap;
use Mage\Sitemap\Test\Page\Adminhtml\SitemapIndex;
use Mage\Sitemap\Test\Page\Adminhtml\SitemapNew;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 *  1. Create category.
 *  2. Create simple product.
 *  3. Create CMS page.
 *
 * Steps:
 *  1. Login to the backend.
 *  2. Navigate to Catalog > Google Sitemap.
 *  3. Click "Add Sitemap" button.
 *  4. Fill out all data according to data set.
 *  5. Click "Save & Generate" button.
 *  6. Perform all assertions.
 *
 * @group XML_Sitemap_(PS)
 * @ZephyrId MPERF-7491
 */
class GenerateSitemapEntityTest extends Injectable
{
    /**
     * Sitemap index page.
     *
     * @var SitemapIndex
     */
    protected $sitemapIndex;

    /**
     * Sitemap new page.
     *
     * @var SitemapNew
     */
    protected $sitemapNew;

    /**
     * Inject data.
     *
     * @param SitemapIndex $sitemapIndex
     * @param SitemapNew $sitemapNew
     * @return void
     */
    public function __inject(SitemapIndex $sitemapIndex, SitemapNew $sitemapNew)
    {
        $this->sitemapIndex = $sitemapIndex;
        $this->sitemapNew = $sitemapNew;
    }

    /**
     * Generate sitemap test.
     *
     * @param Sitemap $sitemap
     * @param CatalogProductSimple $product
     * @param CatalogCategory $category
     * @param CmsPage $cmsPage
     * @return void
     */
    public function test(Sitemap $sitemap, CatalogProductSimple $product, CatalogCategory $category, CmsPage $cmsPage)
    {
        // Preconditions
        $product->persist();
        $category->persist();
        $cmsPage->persist();

        // Steps
        $this->sitemapIndex->open();
        $this->sitemapIndex->getGridPageActions()->addNew();
        $this->sitemapNew->getSitemapForm()->fill($sitemap);
        $this->sitemapNew->getFormPageActions()->saveAndGenerate();
    }
}
