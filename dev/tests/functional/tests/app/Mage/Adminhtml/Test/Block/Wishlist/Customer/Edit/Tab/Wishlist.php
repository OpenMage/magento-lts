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

namespace Mage\Adminhtml\Test\Block\Wishlist\Customer\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Wishlist\Customer\Edit\Tab\Wishlist\Grid;

/**
 * Customer Wishlist edit tab.
 */
class Wishlist extends Tab
{
    /**
     * Wishlist grid selector.
     *
     * @var string
     */
    protected $wishlistGrid = '#wishlistGrid';

    /**
     * Get wishlist grid.
     *
     * @return Grid
     */
    public function getSearchGridBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Wishlist\Customer\Edit\Tab\Wishlist\Grid',
            ['element' => $this->_rootElement->find($this->wishlistGrid)]
        );
    }
}
