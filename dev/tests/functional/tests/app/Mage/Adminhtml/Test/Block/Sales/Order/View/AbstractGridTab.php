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

namespace Mage\Adminhtml\Test\Block\Sales\Order\View;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Widget\Grid;

/**
 * Order abstract grid tab.
 */
abstract class AbstractGridTab extends Tab
{
    /**
     * Grid selector.
     *
     * @var string
     */
    protected $gridSelector;

    /**
     * Grid class.
     *
     * @var string
     */
    protected $gridClass;

    /**
     * Get order type grid.
     *
     * @return Grid
     */
    public function getGrid()
    {
        return $this->blockFactory->create(
            $this->gridClass,
            ['element' => $this->_rootElement->find($this->gridSelector)]
        );
    }
}
