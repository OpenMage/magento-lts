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

namespace Mage\Sales\Test\Block\Order;

use Mage\Sales\Test\Fixture\Order;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;

/**
 * Order history block on My Order page.
 */
class History extends Block
{
    /**
     * Item order.
     *
     * @var string
     */
    protected $itemOrder = '//tr[td[contains(@class, "number") and normalize-space(.)="%s"]]';

    /**
     * View button css selector.
     *
     * @var string
     */
    protected $viewButton = '[href*="order/view"]';

    /**
     * Get item order block.
     *
     * @param string $id
     * @return Element
     */
    protected function searchOrderById($id)
    {
        return $this->_rootElement->find(sprintf($this->itemOrder, $id), Locator::SELECTOR_XPATH);
    }

    /**
     * Open item order.
     *
     * @param string $id
     * @return void
     */
    public function openOrderById($id)
    {
        $this->searchOrderById($id)->find($this->viewButton)->click();
    }

    /**
     * Check if order is visible.
     *
     * @param Order|string $order
     * @return bool
     */
    public function isOrderVisible($order)
    {
        $orderId = $order instanceof Order ? $order->getId() : $order;
        return $this->searchOrderById($orderId)->isVisible();
    }
}
