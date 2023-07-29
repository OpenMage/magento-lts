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

namespace Mage\Wishlist\Test\Block\Customer;

use Magento\Mtf\Block\Block;

/**
 * Wish list details block in "My Wish List" page.
 */
class Wishlist extends Block
{
    /**
     * Button 'Update Wish List' css selector.
     *
     * @var string
     */
    protected $updateButton = '.buttons-set2 .btn-update';

    /**
     * Empty block css selector.
     *
     * @var string
     */
    protected $empty = '.wishlist-empty';

    /**
     * Click button 'Update Wish List'.
     *
     * @return void
     */
    public function clickUpdateWishlist()
    {
        $this->_rootElement->find($this->updateButton)->click();
    }

    /**
     * Check empty block visible.
     *
     * @return bool
     */
    public function isEmptyBlockVisible()
    {
        return $this->_rootElement->find($this->empty)->isVisible();
    }
}
