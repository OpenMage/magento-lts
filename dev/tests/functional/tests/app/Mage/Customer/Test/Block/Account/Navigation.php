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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Customer\Test\Block\Account;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Navigation block on customer account page.
 */
class Navigation extends Block
{
    /**
     * Navigation item link selector.
     *
     * @var string
     */
    protected $navigationItem = './/a[contains(.,"%s")]';

    /**
     * Click to navigation item link.
     *
     * @param string $link
     * @return void
     */
    public function openNavigationItem($link)
    {
        $this->_rootElement->find(sprintf($this->navigationItem, $link), Locator::SELECTOR_XPATH)->click();
    }
}
