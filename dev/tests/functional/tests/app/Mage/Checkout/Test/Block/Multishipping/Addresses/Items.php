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

namespace Mage\Checkout\Test\Block\Multishipping\Addresses;

use Mage\Checkout\Test\Block\Multishipping\AbstractMultishipping\AbstractItems;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Client\ElementInterface;

/**
 * Items block on checkout with multishipping address page.
 */
class Items extends AbstractItems
{
    /**
     * Selector for item block.
     *
     * @var string
     */
    protected $itemBlock = '//tbody/tr[.//a[text()="%s"]]';

    /**
     * Selector for 'Update Qty & Addresses' button.
     *
     * @var string
     */
    protected $updateDataButton = 'button[type="submit"]';

    /**
     * Get item block.
     *
     * @param InjectableFixture $product
     * @param int $itemIndex
     * @return ElementInterface
     */
    public function getItemBlockElement(InjectableFixture $product, $itemIndex)
    {
        $itemBlockSelector = sprintf($this->itemBlock, $product->getName());
        ++$itemIndex;
        $itemBlockElement = $this->_rootElement->find($itemBlockSelector . "[$itemIndex]", Locator::SELECTOR_XPATH);
        if (!$itemBlockElement->isVisible()) {
            $itemBlockElement = $this->_rootElement->find($itemBlockSelector, Locator::SELECTOR_XPATH);
        }

        return $itemBlockElement;
    }

    /**
     * Get path for items class.
     *
     * @return string
     */
    protected function getItemBlockClass()
    {
        return 'Mage\Checkout\Test\Block\Multishipping\Addresses\Items\Item';
    }

    /**
     * Click on 'Update Qty & Addresses' button.
     */
    public function updateData()
    {
        $this->_rootElement->find($this->updateDataButton)->click();
    }
}
