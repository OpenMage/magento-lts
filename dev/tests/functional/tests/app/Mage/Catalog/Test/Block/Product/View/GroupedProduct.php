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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Block\Product\View;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Grouped product block.
 */
class GroupedProduct extends Block
{
    /**
     * Selector for sub product block by name.
     *
     * @var string
     */
    protected $subProductByName = './/tr[td[contains(@class,"name")]/p[contains(.,"%s")]]';

    /**
     * Selector for sub product name.
     *
     * @var string
     */
    protected $productName = '.name-wrapper';

    /**
     * Selector for qty of sub product.
     *
     * @var string
     */
    protected $qty = '[name^="super_group"]';

    /**
     * Return product options on view page.
     *
     * @param InjectableFixture $product
     * @return array
     */
    public function getOptions(InjectableFixture $product)
    {
        $options = [];
        $associatedProducts = $product->getAssociated();

        foreach ($associatedProducts as $product) {
            $subProductBlock = $this->_rootElement->find(
                sprintf($this->subProductByName, $product['name']),
                Locator::SELECTOR_XPATH
            );

            $options[] = [
                'name' => $subProductBlock->find($this->productName)->getText(),
                'qty' => $subProductBlock->find($this->qty)->getValue(),
            ];
        }

        return $options;
    }

    /**
     * Get grouped item block.
     *
     * @param string $key
     * @return GroupedItemForm
     */
    public function getGroupedItemForm($key)
    {
        return $this->blockFactory->create(
            'Mage\Catalog\Test\Block\Product\View\GroupedItemForm',
            ['element' => $this->_rootElement->find(sprintf($this->subProductByName, $key), Locator::SELECTOR_XPATH)]
        );
    }
}
