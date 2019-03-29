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

namespace Mage\Downloadable\Test\Block\Catalog\Product;

use Mage\Downloadable\Test\Fixture\DownloadableProduct;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Downloadable\Test\Block\Catalog\Product\View\Links;
use Mage\Downloadable\Test\Block\Catalog\Product\View\Samples;

/**
 * Downloadable product view block on the product page.
 */
class View extends \Mage\Catalog\Test\Block\Product\View
{
    /**
     * Block Downloadable links.
     *
     * @var string
     */
    protected $blockDownloadableLinks = 'div#product-options-wrapper';

    /**
     * Block Downloadable samples.
     *
     * @var string
     */
    protected $blockDownloadableSamples = 'div.add-to-cart-wrapper dl';

    /**
     * Get downloadable link block.
     *
     * @return Links
     */
    public function getDownloadableLinksBlock()
    {
        return $this->blockFactory->create(
            'Mage\Downloadable\Test\Block\Catalog\Product\View\Links',
            ['element' => $this->_rootElement->find($this->blockDownloadableLinks)]
        );
    }

    /**
     * Get downloadable samples block.
     *
     * @return Samples
     */
    public function getDownloadableSamplesBlock()
    {
        return $this->blockFactory->create(
            'Mage\Downloadable\Test\Block\Catalog\Product\View\Samples',
            ['element' => $this->_rootElement->find($this->blockDownloadableSamples)]
        );
    }

    /**
     * Filling the options specified for the product.
     *
     * @param InjectableFixture $product
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fillOptions(InjectableFixture $product)
    {
        /** @var DownloadableProduct $product */
        $downloadableLinks = isset($product->getDownloadableLinks()['downloadable']['link'])
            ? $product->getDownloadableLinks()['downloadable']['link']
            : [];
        $checkoutData = $product->getCheckoutData();

        if (isset($checkoutData['options'])) {
            foreach ($checkoutData['options']['links'] as $key => $linkData) {
                $linkKey = str_replace('link_', '', $linkData['label']);
                $linkData['label'] = $downloadableLinks[$linkKey]['title'];
                $checkoutData['options']['links'][$key] = $linkData;
            }
            $this->getDownloadableLinksBlock()->fill($checkoutData['options']['links']);
        }

        parent::fillOptions($product);
    }

    /**
     * Return product options.
     *
     * @param InjectableFixture $product
     * @return array
     */
    public function getOptions(InjectableFixture $product)
    {
        $downloadableOptions = [];

        if ($this->_rootElement->find($this->blockDownloadableLinks)->isVisible()) {
            $downloadableOptions['downloadable_links'] = [
                'title' => $this->getDownloadableLinksBlock()->getTitle(),
                'downloadable' => [
                    'link' => $this->getDownloadableLinksBlock()->getLinks(),
                ],
            ];
        }
        if ($this->_rootElement->find($this->blockDownloadableSamples)->isVisible()) {
            $downloadableOptions['downloadable_sample'] = [
                'title' => $this->getDownloadableSamplesBlock()->getTitle(),
                'downloadable' => [
                    'sample' => $this->getDownloadableSamplesBlock()->getLinks(),
                ],
            ];
        }

        return ['downloadable_options' => $downloadableOptions] + parent::getOptions($product);
    }

    /**
     * Get text of Stock Availability control.
     *
     * @return string
     */
    public function getDownloadableStockAvailability()
    {
        return strtolower($this->_rootElement->find($this->stockAvailability)->getText());
    }
}
