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

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create;

use Mage\Adminhtml\Test\Fixture\Store as StoreFixture;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Adminhtml sales order create select store block.
 */
class Store extends Block
{
    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Selector for store label.
     *
     * @var string
     */
    protected $storeLabel = '//label[text()="%s"]/preceding-sibling::*';

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Select store view for order based on Order fixture.
     *
     * @param StoreFixture|null $store
     * @return void
     */
    public function selectStoreView(StoreFixture $store = null)
    {
        if (!$this->isVisible()) {
            return;
        }
        $storeName = $store == null ? 'Default Store View' : $store->getName();
        $selector = sprintf($this->storeLabel, $storeName);
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH, 'checkbox')->setValue('Yes');
        $this->getTemplateBlock()->waitLoader();
    }
}
