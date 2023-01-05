<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Downloadable\Test\Constraint;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Downloadable\Test\Fixture\DownloadableProduct;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractAssertForm;

/**
 * Assert that Samples links are present on the downloadable product on front-end.
 */
class AssertDownloadableSamplesData extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * List downloadable sample links fields for verify.
     *
     * @var array
     */
    protected $downloadableSampleField = [
        'title',
        'downloadable',
    ];

    /**
     * List fields of downloadable sample link for verify.
     *
     * @var array
     */
    protected $linkField = [
        'title',
    ];

    /**
     * Assert that Samples links are present on the downloadable product on front-end.
     *
     * @param CatalogProductView $productView
     * @param DownloadableProduct $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductView $productView, DownloadableProduct $product, Browser $browser)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        $fixtureSampleLinks = $this->prepareFixtureData($product);
        $pageOptions = $productView->getViewBlock()->getOptions($product);
        $pageSampleLinks = isset($pageOptions['downloadable_options']['downloadable_sample'])
            ? $this->preparePageData($pageOptions['downloadable_options']['downloadable_sample'])
            : [];
        $error = $this->verifyData($fixtureSampleLinks, $pageSampleLinks);
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
        $data = $this->sortDataByPath($product->getDownloadableSample(), 'downloadable/sample::sort_order');

        foreach ($data['downloadable']['sample'] as $key => $link) {
            $link = array_intersect_key($link, array_flip($this->linkField));
            $data['downloadable']['sample'][$key] = $link;
        }
        $data = array_intersect_key($data, array_flip($this->downloadableSampleField));

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
        foreach ($data['downloadable']['sample'] as $key => $link) {
            $link = array_intersect_key($link, array_flip($this->linkField));
            $data['downloadable']['sample'][$key] = $link;
        }
        $data = array_intersect_key($data, array_flip($this->downloadableSampleField));

        return $data;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sample links for downloadable product are present on product page.';
    }
}
