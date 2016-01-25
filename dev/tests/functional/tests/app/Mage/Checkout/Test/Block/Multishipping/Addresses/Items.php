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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
