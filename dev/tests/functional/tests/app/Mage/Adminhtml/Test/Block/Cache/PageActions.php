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

namespace Mage\Adminhtml\Test\Block\Cache;

/**
 * Page actions block.
 */
class PageActions extends \Mage\Adminhtml\Test\Block\PageActions
{
    /**
     * 'Flush Cache Storage' button.
     *
     * @var string
     */
    protected $flushCacheStorageButton = '[onClick*="cache/flushAll"]';

    /**
     * Flush cache storage.
     *
     * @return void
     */
    public function flushCacheStorage()
    {
        $this->_rootElement->find($this->flushCacheStorageButton)->click();
        $this->browser->acceptAlert();
    }
}
