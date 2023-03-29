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

namespace Mage\Adminhtml\Test\Block\Sales\Order\View\Tab;

use Mage\Adminhtml\Test\Block\Sales\Order\View\AbstractGridTab;

/**
 * Order transactions tab.
 */
class Transactions extends AbstractGridTab
{
    /**
     * Grid selector.
     *
     * @var string
     */
    protected $gridSelector = '#order_transactions';

    /**
     * Grid class.
     *
     * @var string
     */
    protected $gridClass = 'Mage\Adminhtml\Test\Block\Sales\Transactions\Grid';
}
