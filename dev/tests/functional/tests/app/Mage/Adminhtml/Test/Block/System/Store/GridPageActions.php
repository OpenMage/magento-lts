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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\System\Store;

/**
 * Grid page actions block in Cms Block grid page.
 */
class GridPageActions extends \Mage\Adminhtml\Test\Block\GridPageActions
{
    /**
     * Add Store View button.
     *
     * @var string
     */
    protected $addStoreViewButton = 'button[onclick*="newStore"]';

    /**
     * "Create Store" button selector.
     *
     * @var string
     */
    protected $createStoreButton = 'button[onclick*="newGroup"]';

    /**
     * "Create Website" button selector.
     *
     * @var string
     */
    protected $createWebsiteButton = 'button[onclick*="newWebsite"]';

    /**
     * Click on Add Store View button.
     *
     * @return void
     */
    public function addStoreView()
    {
        $this->_rootElement->find($this->addStoreViewButton)->click();
    }

    /**
     * Click on "Create Store" button.
     *
     * @return void
     */
    public function createStoreGroup()
    {
        $this->_rootElement->find($this->createStoreButton)->click();
    }

    /**
     * Click on "Create Website" button.
     *
     * @return void
     */
    public function createWebsite()
    {
        $this->_rootElement->find($this->createWebsiteButton)->click();
    }
}
