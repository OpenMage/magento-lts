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

namespace Mage\Bundle\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Block\Product\View;
use Mage\Catalog\Test\Constraint\AssertProductTierPriceOnProductPage;

/**
 * Assert that displayed tier price on bundle product page equals passed from fixture.
 */
class AssertProductTierPriceOnBundleProductPage extends AssertProductTierPriceOnProductPage
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Regular expression pattern for parse tier price.
     *
     * @var string
     */
    protected $pattern = '#.* (\d+) \w+ (\d+)%.*#i';

    /**
     * Count matches.
     *
     * @var int
     */
    protected $matchCount = 2;

    /**
     * Fields for verify.
     *
     * @var array
     */
    protected $verifyFields = [
        'price_qty',
        'price'
    ];

    /**
     * Prepare tier price data.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function prepareTierPrices(InjectableFixture $product)
    {
        $tierPrices = $product->getTierPrice();
        foreach ($tierPrices as $key => $tierPrice) {
            $tierPrices[$key]['price'] = number_format($tierPrices[$key]['price'], $this->priceFormat);
        }

        return $tierPrices;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Tier price is displayed on the bundle product page.';
    }
}
