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

use Mage\Checkout\Test\Block\Multishipping\Addresses\Items;
use Magento\Mtf\Client\ElementInterface;

/**
 * Checkout multishipping addresses block.
 */
class Addresses extends AbstractMultishipping
{
    /**
     * Selector for 'Enter a New Address' button.
     *
     * @var string
     */
    protected $enterNewAddress = '[data-action="add-new-customer-address"]';

    /**
     * Selector for items block.
     *
     * @var string
     */
    protected $itemsBlock = '#multiship-addresses-table';

    /**
     * Selector for 'Continue to Shipping Information' button.
     *
     * @var string
     */
    protected $continue = '[data-action="checkout-continue-shipping"]';

    /**
     * Click on 'Enter a New Address' button.
     *
     * @return void
     */
    public function clickEnterNewAddress()
    {
        $this->_rootElement->find($this->enterNewAddress)->click();
    }

    /**
     * Get path for items class.
     *
     * @return string
     */
    protected function getItemsClass()
    {
        return 'Mage\Checkout\Test\Block\Multishipping\Addresses\Items';
    }

    /**
     * Get items block element.
     *
     * @return ElementInterface
     */
    protected function getItemsBlockElement()
    {
        return $this->_rootElement->find($this->itemsBlock);
    }
}
