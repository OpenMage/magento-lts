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

namespace Mage\Catalog\Test\Block\Msrp;

use Magento\Mtf\Block\Block;

/**
 * MSRP popup block.
 */
class Popup extends Block
{
    /**
     * Price box selector.
     *
     * @var string
     */
    protected $priceBox = '.price-box';

    /**
     * Escape currency and separator for price.
     *
     * @param string $price
     * @param string $currency
     * @return string
     */
    protected function escape($price, $currency = '$')
    {
        return str_replace($currency, '', $price);
    }

    /**
     * Get MAP text.
     *
     * @return string
     */
    public function getMap()
    {
        return $this->escape($this->_rootElement->find($this->priceBox)->getText());
    }

    /**
     * Check if price visible in popup dialog.
     *
     * @return bool
     */
    public function isPriceVisible()
    {
        return $this->_rootElement->find($this->priceBox)->isVisible();
    }
}
