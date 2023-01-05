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

namespace Mage\Checkout\Test\Block\Multishipping;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\ElementInterface;
use Mage\Checkout\Test\Block\Multishipping\Shipping\Items as ShippingItems;
use Mage\Checkout\Test\Block\Multishipping\Addresses\Items as AddressItems;

/**
 * Checkout multishipping abstract block.
 */
abstract class AbstractMultishipping extends Block
{
    /**
     * Selector for 'Continue' button.
     *
     * @var string
     */
    protected $continue;

    /**
     * Get path for items class.
     *
     * @return string
     */
    protected abstract function getItemsClass();

    /**
     * Get items block element.
     *
     * @return ElementInterface
     */
    protected abstract function getItemsBlockElement();

    /**
     * Get items block.
     *
     * @return ShippingItems|AddressItems
     */
    public function getItemsBlock()
    {
        return $this->blockFactory->create($this->getItemsClass(), ['element' => $this->getItemsBlockElement()]);
    }

    /**
     * Click on 'Continue' button.
     *
     * @return void
     */
    public function clickContinueButton()
    {
        $this->_rootElement->find($this->continue)->click();
    }
}
