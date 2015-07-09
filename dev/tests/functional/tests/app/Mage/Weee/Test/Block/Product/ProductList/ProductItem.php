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
