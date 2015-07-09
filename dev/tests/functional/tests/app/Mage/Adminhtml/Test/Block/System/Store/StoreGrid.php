<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\System\Store;

use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Fixture\Store;
use Mage\Adminhtml\Test\Block\Widget\Grid;
use Mage\Adminhtml\Test\Fixture\StoreGroup;

/**
 * Adminhtml Store View management grid.
 */
class StoreGrid extends Grid
{
    /**
     * Store title format for XPATH.
     *
     * @var string
     */
    protected $titleFormat = '//td[a[contains(@href, "store_id") and contains(., "%s")]]';

    /**
     * Store name link selector.
     *
     * @var string
     */
    protected $storeName = '//a[contains(@href,"store_id") and contains(text(), "%s")]';

    /**
     * Store group name link selector.
     *
     * @var string
     */
    protected $storeGroupName = '//a[contains(@href,"group_id") and contains(text(), "%s")]';

    /**
     * Website name link selector.
     *
     * @var string
     */
    protected $websiteName = '//a[contains(@href,"website_id") and contains(text(), "%s")]';

    /**
     * General link selector.
     *
     * @var string
     */
    protected $link = '//a[contains(.,"%s")]';

    /**
     * Check if store exists.
     *
     * @param string $title
     * @return bool
     */
    public function isStoreExists($title)
    {
        $element = $this->_rootElement->find(sprintf($this->titleFormat, $title), Locator::SELECTOR_XPATH);
        return $element->isVisible();
    }

    /**
     * Check if store group exists.
     *
     * @param string $title
     * @return bool
     */
    public function isStoreGroupExists($title)
    {
        return $this->_rootElement->find(sprintf($this->storeGroupName, $title), Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Open Store View.
     *
     * @param Store $store
     * @return void
     */
    public function openStore(Store $store)
    {
        $this->_rootElement->find(sprintf($this->storeName, $store->getName()), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Open Store Group.
     *
     * @param string $title
     * @return void
     */
    public function openStoreGroup($title)
    {
        $this->_rootElement->find(sprintf($this->storeGroupName, $title), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Open website by name.
     *
     * @param string $title
     * @return void
     */
    public function openWebsite($title)
    {
        $this->_rootElement->find(sprintf($this->websiteName, $title), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Open Store Group by name.
     *
     * @param string $storeGroupName
     * @return void
     */
    public function openStoreGroupByName($storeGroupName)
    {
        $this->_rootElement->find(sprintf($this->storeGroupName, $storeGroupName), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get link url.
     *
     * @param string $link
     * @throws \Exception
     * @return string
     */
    public function getLinkUrl($link)
    {
        $link = $this->_rootElement->find(sprintf($this->link, $link), Locator::SELECTOR_XPATH);
        if ($link->isVisible()) {
            return $link->getAttribute('href');
        } else {
            throw new \Exception("Link should be visible to take it's href");
        }
    }
}
