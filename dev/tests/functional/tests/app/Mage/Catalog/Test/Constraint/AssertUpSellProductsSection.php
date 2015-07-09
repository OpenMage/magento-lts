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

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that products are displayed in up-sell section.
 */
class AssertUpSellProductsSection extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Assert that products are displayed in up-sell section.
     *
     * @param Browser $browser
     * @param FixtureInterface $product
     * @param array $upSellProducts
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function processAssert(
        Browser $browser,
        FixtureInterface $product,
        array $upSellProducts,
        CatalogProductView $catalogProductView
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        foreach ($upSellProducts as $upSellProduct) {
            \PHPUnit_Framework_Assert::assertTrue(
                $catalogProductView->getUpsellBlock()->getItemBlock($upSellProduct)->isVisible(),
                "Product {$upSellProduct->getName()} is absent in up-sells products."
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Products are displayed in up-sell section.';
    }
}
