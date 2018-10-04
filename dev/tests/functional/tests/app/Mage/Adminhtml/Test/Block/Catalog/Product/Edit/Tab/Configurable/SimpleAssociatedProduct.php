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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
