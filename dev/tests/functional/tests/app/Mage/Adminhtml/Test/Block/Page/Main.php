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

namespace Mage\Adminhtml\Test\Block\Page;

use Magento\Mtf\Block\Block;

/**
 * Main dashboard block.
 */
class Main extends Block
{
    /**
     * Selector for Revenue prices.
     *
     * @var string
     */
    protected $revenuePriceBlock = '#dashboard_diagram_totals td:first-child';

    /**
     * Get Revenue price.
     *
     * @return string
     */
    public function getRevenuePrice()
    {
        return $this->_rootElement->find($this->revenuePriceBlock)->getText();
    }

    /**
     * Get Main Block text.
     *
     * @return string
     */
    public function getMainBlockText()
    {
        return $this->_rootElement->getText();
    }
}
