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

namespace Mage\Shipping\Test\Block\Sales\Order\Shipments;

use Magento\Mtf\Block\Block;
use Mage\Shipping\Test\Block\Sales\Order\Shipments\Shipment\Items;

/**
 * Item shipment block.
 */
class Shipment extends Block
{
    /**
     * Items block selector.
     *
     * @var string
     */
    protected $itemsSelector = '#my-shipment-table-%d';

    /**
     * Get items block.
     *
     * @return Items
     */
    public function getItemsBlock()
    {
        return $this->blockFactory->create(
            'Mage\Shipping\Test\Block\Sales\Order\Shipments\Shipment\Items',
            ['element' => $this->_rootElement->find(sprintf($this->itemsSelector, $this->config['id']))]
        );
    }
}
