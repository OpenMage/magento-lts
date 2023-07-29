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
