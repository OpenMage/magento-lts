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

namespace Mage\Weee\Test\Block\Product\ProductList;

use Magento\Mtf\Block\Block;
use Mage\Weee\Test\Block\Product\Price;

/**
 * Product item block on frontend category view.
 */
class ProductItem extends Block
{
    /**
     * Selector for price block class.
     *
     * @var string
     */
    protected $priceBlockClass = '.price-box';

    /**
     * Return price block.
     *
     * @return Price
     */
    public function getPriceBlock()
    {
        return $this->blockFactory->create(
            'Mage\Weee\Test\Block\Product\Price',
            ['element' => $this->_rootElement->find($this->priceBlockClass)]
        );
    }
}
