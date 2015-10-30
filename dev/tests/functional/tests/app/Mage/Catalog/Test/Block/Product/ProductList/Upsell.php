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

namespace Mage\Catalog\Test\Block\Product\ProductList;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Block\Product\ProductList\Upsell\Item;

/**
 * Upsell products block.
 */
class Upsell extends Block
{
    /**
     * Upsell product locator on the page.
     *
     * @var string
     */
    protected $upsellProduct = "//li[normalize-space(h3//a)='%s']";

    /**
     * Get item block.
     *
     * @param InjectableFixture $product
     * @return Item
     */
    public function getItemBlock(InjectableFixture $product)
    {
        return $this->blockFactory->create(
            'Mage\Catalog\Test\Block\Product\ProductList\Upsell\Item',
            [
                'element' => $this->_rootElement->find(
                    sprintf($this->upsellProduct, $product->getName()),
                    Locator::SELECTOR_XPATH
                )
            ]
        );
    }

}
