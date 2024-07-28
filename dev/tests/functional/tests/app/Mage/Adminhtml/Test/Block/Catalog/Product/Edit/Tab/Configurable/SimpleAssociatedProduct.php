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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Simple associated product block.
 */
class SimpleAssociatedProduct extends Block
{
    /**
     * Selector for 'Create Empty' button.
     *
     * @var string
     */
    protected $createEmpty = '[onclick="superProduct.createEmptyProduct()"]';

    /**
     * Selector for 'Copy From Configurable' button.
     *
     * @var string
     */
    protected $copyFromConfigurable = '[onclick="superProduct.createNewProduct()"]';

    /**
     * Click on 'Create Empty' button.
     *
     * @return void
     */
    public function clickCreateEmpty()
    {
        $this->browser->find(".//h4[text()='Create Simple Associated Product']",  Locator::SELECTOR_XPATH)->hover();
        $this->_rootElement->find($this->createEmpty)->click();
    }

    /**
     * Click on 'Copy From Configurable' button.
     *
     * @return void
     */
    public function clickCopyFromConfigurable()
    {
        $this->_rootElement->find($this->copyFromConfigurable)->click();
    }
}
