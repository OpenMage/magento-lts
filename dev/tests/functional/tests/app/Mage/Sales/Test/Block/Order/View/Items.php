<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Block\Order\View;

use Mage\Adminhtml\Test\Block\Sales\Order\AbstractItemsNewBlock;

/**
 * Block for items ordered on order page.
 */
class Items extends AbstractItemsNewBlock
{
    /**
     * Item block class.
     *
     * @var string
     */
    protected $classItemBlock = 'Mage\Sales\Test\Block\Order\View\Items\Product';
}
