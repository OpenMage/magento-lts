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

namespace Mage\Downloadable\Test\Constraint;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Downloadable\Test\Fixture\DownloadableProduct;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractAssertForm;

/**
 * Assert that Links for downloadable product are present on product page.
 */
class AssertDownloadableLinksData extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * List downloadable link fields for verify.
     *
     * @var array
     */
    protected $downloadableLinksField = [
        'title',
        'downloadable',
    ];

    /**
     * List fields of downloadable link for verify.
     *
     * @var array
     */
    protected $linkField = [
        'title',
        'links_purchased_separately',
        'price',
    ];

    /**
     * Assert that Links for downloadable product are present on product page.
     *
     * @param CatalogProductView $catalogProductView
     * @param DownloadableProduct $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        DownloadableProduct $product,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        $fixtureDownloadableLinks = $this->prepareFixtureData($product);
        $pageOptions = $catalogProductView->getViewBlock()->getOptions($product);
        $pageDownloadableLinks = $this->preparePageData($pageOptions['downloadable_options']['downloadable_links']);
        $error = $this->verifyData($fixtureDownloadableLinks, $pageDownloadableLinks);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Prepare fixture data for verify.
     *
     * @param DownloadableProduct $product
     * @return array
     */
    protected function prepareFixtureData(DownloadableProduct $product)
    {
        $data = $this->sortDataByPath($product->getDownloadableLinks(), 'downloadable/link::sort_order');

        foreach ($data['downloadable']['link'] as $key => $link) {
            $link['links_purchased_separately'] = $data['links_purchased_separately'];
            $link = array_intersect_key($link, array_flip($this->linkField));

            $data['downloadable']['link'][$key] = $link;
        }
        $data = array_intersect_key($data, array_flip($this->downloadableLinksField));

        return $data;
    }

    /**
     * Prepare page data for verify.
     *
     * @param array $data
     * @return array
     */
    protected function preparePageData(array $data)
    {
        foreach ($data['downloadable']['link'] as $key => $link) {
            $link = array_intersect_key($link, array_flip($this->linkField));
            $data['downloadable']['link'][$key] = $link;
        }
        $data = array_intersect_key($data, array_flip($this->downloadableLinksField));

        return $data;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Links for downloadable product are present on product page';
    }
}
