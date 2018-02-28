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
