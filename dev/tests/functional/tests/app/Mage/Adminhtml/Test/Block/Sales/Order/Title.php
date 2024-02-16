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
