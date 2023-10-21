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
