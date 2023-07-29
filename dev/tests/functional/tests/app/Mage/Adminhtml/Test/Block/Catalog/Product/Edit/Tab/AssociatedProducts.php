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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\AssociatedProducts\Grid;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\AssociatedProducts\Product;

/**
 * Associated products tab.
 */
class AssociatedProducts extends Tab
{
    /**
     * Associated products grid selector.
     *
     * @var string
     */
    protected $productSearchGrid = "#super_product_grid";

    /**
     * Product row selector.
     *
     * @var string
     */
    protected $productRow = "//*[@id='super_product_grid_table']/tbody/tr[%d]";

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (isset($fields['associated'])) {
            $gridBlock = $this->getSearchGridBlock();
            foreach ($fields['associated']['value'] as $key => $groupedProduct) {
                $gridBlock->searchAndSelect(['name' => $groupedProduct['name'], 'id' => $groupedProduct['id']]);
                $this->getProductBlock()->fillOptions($groupedProduct);
            }
        }
        return $this;
    }

    /**
     * Get associated products grid.
     *
     * @return Grid
     */
    protected function getSearchGridBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\AssociatedProducts\Grid',
            ['element' => $this->_rootElement->find($this->productSearchGrid)]
        );
    }

    /**
     * Get product row block.
     *
     * @param int $key [optional]
     * @return Product
     */
    protected function getProductBlock($key = 1)
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\AssociatedProducts\Product',
            ['element' => $this->_rootElement->find(sprintf($this->productRow, $key), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $result = [];
        $options = [];
        foreach ($fields['associated']['value'] as $key => $field) {
            $options[$key] = $this->getProductBlock($key + 1)->getOptions($field);
        }
        $result['associated'] = array_reverse($options);

        return $result;
    }
}
