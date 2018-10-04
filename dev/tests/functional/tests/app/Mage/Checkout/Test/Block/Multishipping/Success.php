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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block\Multishipping;

use Magento\Mtf\Block\Block;

/**
 * Checkout multishipping success block.
 */
class Success extends Block
{
    /**
     * Selector for orders ids.
     *
     * @var string
     */
    protected $ordersIds = '[data-role="order-numbers"] a';

    /**
     * Get orders ids.
     *
     * @return array
     */
    public function getOrdersIds()
    {
        $ordersIds = [];
        $ordersIdsElements = $this->_rootElement->getElements($this->ordersIds);
        foreach ($ordersIdsElements as $orderIdElement) {
            $ordersIds[] = $orderIdElement->getText();
        }

        return $ordersIds;
    }
}
