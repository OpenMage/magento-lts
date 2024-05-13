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

namespace Mage\Adminhtml\Test\Block\Sales\Order;

use Magento\Mtf\Block\Block;
use Mage\Adminhtml\Test\Block\Sales\Order\AbstractItems\AbstractItem;

/**
 * Base Items block on Credit Memo, Invoice, Shipment view page.
 */
class AbstractItems extends Block
{
    /**
     * Item class.
     *
     * @var string
     */
    protected $itemClass;

    /**
     * Locator for row item.
     *
     * @var string
     */
    protected $rowItem = '.data.order-tables>tbody>tr';

    /**
     * Get items data.
     *
     * @return array
     */
    public function getData()
    {
        $result = [];
        foreach ($this->getItems() as $item) {
            /** @var AbstractItem $item */
            $result[] = $item->getFieldsData();
        }

        return $result;
    }

    /**
     * Get items blocks.
     *
     * @return AbstractItem[]
     */
    protected function getItems()
    {
        $items = $this->_rootElement->getElements($this->rowItem);
        foreach ($items as $key => $item) {
            $items[$key] = $this->blockFactory->create($this->itemClass, ['element' => $item]);
        }

        return $items;
    }
}
