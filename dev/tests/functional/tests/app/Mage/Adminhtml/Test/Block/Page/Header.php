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

namespace Mage\Adminhtml\Test\Block\Page;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Header block.
 */
class Header extends Block
{
    /**
     * Logout link selector.
     *
     * @var string
     */
    protected $logOut = '.link-logout';

    /**
     * Top menu item selector.
     *
     * @var string
     */
    protected $topMenuItem = './/div[@class = "nav-bar"]/ul/li/a[contains(., "%s")]';

    /**
     * Log out Admin User.
     *
     * @return void
     */
    public function logOut()
    {
        $this->_rootElement->find($this->logOut)->click();
        $this->waitForElementNotVisible($this->logOut);
    }

    /**
     * First level menu items selector.
     *
     * @var string
     */
    protected $firstLevelMenuItem = 'li.parent.level0';

    /**
     * Check navigation menu.
     *
     * @param string $name
     * @return bool
     */
    public function checkMenu($name)
    {
        return $this->_rootElement->find(sprintf($this->topMenuItem, $name), Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Wait header block is visible.
     *
     * @return bool
     */
    public function waitVisible()
    {
        try {
            $browser = $this->_rootElement;
            return $browser->waitUntil(
                function () use ($browser) {
                    return $browser->isVisible() ? true : null;
                }
            );
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get first-level menu elements.
     *
     * @return array
     */
    public function getMenuFirstLevelItems()
    {
        $elements = $this->_rootElement->getElements($this->firstLevelMenuItem);
        $items = [];
        foreach ($elements as $element) {
            $items[] = $element->getText();
        }
        return $items;
    }
}
