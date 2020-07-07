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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
