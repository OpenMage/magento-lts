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

namespace Mage\Checkout\Test\Block\Multishipping;

use Magento\Mtf\Client\ElementInterface;

/**
 * Checkout multishipping shipping block.
 */
class Shipping extends AbstractMultishipping
{
    /**
     * Selector for 'Continue to Shipping Information' button.
     *
     * @var string
     */
    protected $continue = '[data-action="checkout-continue-billing"]';

    /**
     * Get path for items class.
     *
     * @return string
     */
    protected function getItemsClass()
    {
        return 'Mage\Checkout\Test\Block\Multishipping\Shipping\Items';
    }

    /**
     * Get items block element.
     *
     * @return ElementInterface
     */
    protected function getItemsBlockElement()
    {
        return $this->_rootElement;
    }
}
