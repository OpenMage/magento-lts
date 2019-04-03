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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
