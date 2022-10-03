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

namespace Mage\Adminhtml\Test\Block\Sales\Order;

use Magento\Mtf\Block\Block;
/**
 * Order title block.
 */
class Title extends Block
{
    /**
     * Title selector.
     *
     * @var string
     */
    protected $title = '.head-sales-order';

    /**
     * Get title text.
     *
     * @return string|null
     */
    public function getOrderId()
    {
        preg_match('@.*#\s(\d+)\s.*@', $this->_rootElement->find($this->title)->getText(), $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }
}
