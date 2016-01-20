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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sitemap\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Cms\Test\Fixture\CmsPage;
use Mage\Sitemap\Test\Fixture\Sitemap;
use Mage\Sitemap\Test\Page\Adminhtml\SitemapIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that sitemap file contains correct content.
 */
class AssertSitemapContent extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Assert that sitemap.xml file contains correct content according to dataset:
     *  - product url
     *  - category url
     *  - CMS page url
     *
     * @param CatalogProductSimple $product
     * @param CatalogCategory $category
     * @param CmsPage $cmsPage
     * @param Sitemap $sitemap
     * @param SitemapIndex $sitemapIndex
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CatalogCategory $category,
        CmsPage $cmsPage,
        Sitemap $sitemap,
        SitemapIndex $sitemapIndex
    ) {
        $sitemapIndex->open()->getSitemapGrid()->sortGridByField('sitemap_id');
        $filter = [
            'sitemap_filename' => $sitemap->getSitemapFilename(),
            'sitemap_path' => $sitemap->getSitemapPath(),
        ];
        $sitemapIndex->getSitemapGrid()->search($filter);
        $content = file_get_contents($sitemapIndex->getSitemapGrid()->getLinkForGoogle());
        $frontendUrl = str_replace('index.php/', '', $_ENV['app_frontend_url']);
        $urls = [
            $frontendUrl . $product->getUrlKey() . '.html',
            $frontendUrl . $category->getUrlKey() . '.html',
            $frontendUrl . $cmsPage->getIdentifier(),
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $this->checkContent($content, $urls),
            "File '{$sitemap->getSitemapFilename()}' does not contains correct content."
        );
    }

    /**
     * Check content for the presence urls.
     *
     * @param string $content
     * @param array $urls
     * @return bool
     */
    protected function checkContent($content, $urls)
    {
        foreach ($urls as $url) {
            if (strpos($content, $url) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'File sitemap contains correct content.';
    }
}
