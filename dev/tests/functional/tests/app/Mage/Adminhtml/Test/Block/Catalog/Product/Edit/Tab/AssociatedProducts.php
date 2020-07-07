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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
