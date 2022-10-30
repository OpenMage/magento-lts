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

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Fixture\GroupedProduct;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that displayed grouped price on product page equals passed from fixture.
 */
abstract class AbstractAssertPriceOnGroupedProductPage extends AbstractConstraint
{
    /**
     * Format error message.
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Successful message.
     *
     * @var string
     */
    protected $successfulMessage;

    /**
     * Verify product price on grouped product view page.
     *
     * @param GroupedProduct $product
     * @param CatalogProductView $catalogProductView
     * @param AssertPriceOnProductPageInterface $object
     * @param Browser $browser
     * @param string $typePrice [optional]
     * @return void
     */
    protected function processAssertPrice(
        GroupedProduct $product,
        CatalogProductView $catalogProductView,
        AssertPriceOnProductPageInterface $object,
        Browser $browser,
        $typePrice = ''
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $groupedData = $product->getDataFieldConfig('associated')['source']->getProducts();
        foreach ($groupedData as $key => $subProduct) {
            $catalogProductView->getGroupedProductViewBlock()->{'item' . $typePrice . 'PriceProductBlock'}(++$key);
            $object->setErrorMessage(sprintf($this->errorMessage, $subProduct->getName()));
            $object->assertPrice($subProduct, $catalogProductView->getGroupedProductViewBlock(), 'Grouped');
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return $this->successfulMessage;
    }
}
