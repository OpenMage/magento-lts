<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Block\Product\ProductList;

use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Block\BlockInterface;

/**
 * Crosssell product block on the page.
 */
class Crosssell extends Block
{
    /**
     * Crosssell product locator on the page.
     *
     * @var string
     */
    protected $crosssellProduct = "//div[normalize-space(h3//a)='%s']";

    /**
     * Get item block.
     *
     * @param InjectableFixture $product
     * @return BlockInterface
     */
    public function getItemBlock(InjectableFixture $product)
    {
        return $this->blockFactory->create(
            'Mage\Catalog\Test\Block\Product\ProductList\Crosssell\Item',
            [
                'element' => $this->_rootElement->find(
                    sprintf($this->crosssellProduct, $product->getName()),
                    Locator::SELECTOR_XPATH
                )
            ]
        );
    }
}
