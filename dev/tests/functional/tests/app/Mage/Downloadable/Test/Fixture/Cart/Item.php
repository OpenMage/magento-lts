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

namespace Mage\Downloadable\Test\Fixture\Cart;

use Mage\Downloadable\Test\Fixture\DownloadableProduct;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Data for verify cart item block on checkout page.
 */
class Item extends \Mage\Catalog\Test\Fixture\Cart\Item
{
    /**
     * @constructor
     * @param FixtureInterface $product
     */
    public function __construct(FixtureInterface $product)
    {
        parent::__construct($product);
        /** @var DownloadableProduct $product */
        $checkoutDownloadableOptions = [];
        $checkoutData = $product->getCheckoutData();
        $downloadableOptions = $product->getDownloadableLinks();
        foreach ($checkoutData['options']['links'] as $link) {
            $keyLink = str_replace('link_', '', $link['label']);
            $checkoutDownloadableOptions[] = [
                'title' => $downloadableOptions['title'],
                'value' => $downloadableOptions['downloadable']['link'][$keyLink]['title'],
            ];
        }
        $this->data['options'] += $checkoutDownloadableOptions;
    }
}
