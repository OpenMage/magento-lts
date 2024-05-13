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

namespace Mage\Adminhtml\Test\Block\Sales\Order;

use Mage\Adminhtml\Test\Block\Sales\Order\AbstractForm\Product;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Client\Locator;

/**
 * Items block on Credit Memo, Invoice, Shipment new pages.
 */
abstract class AbstractItemsNewBlock extends Block
{
    /**
     * Item product row selector.
     *
     * @var string
     */
    protected $productItem = '//tr[.//*[contains(text(),"%s")]]';

    /**
     * Item block class.
     *
     * @var string
     */
    protected $classItemBlock;

    /**
     * 'Update Qty's' button css selector.
     *
     * @var string
     */
    protected $updateQty = '.update-button';

    /**
     * Get item product block.
     *
     * @param InjectableFixture $product
     * @return Product
     */
    public function getItemProductBlock(InjectableFixture $product)
    {
        $element = $this->_rootElement->find(sprintf($this->productItem, $product->getName()), Locator::SELECTOR_XPATH);
        return $this->blockFactory->create($this->classItemBlock, ['element' => $element]);
    }

    /**
     * Click update qty button.
     *
     * @return void
     */
    public function clickUpdateQty()
    {
        $this->_rootElement->find($this->updateQty)->click();
    }
}
