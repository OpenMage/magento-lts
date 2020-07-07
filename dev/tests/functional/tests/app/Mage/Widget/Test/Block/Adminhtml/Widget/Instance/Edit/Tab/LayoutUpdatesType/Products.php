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

namespace Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType;

use Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\Product\Grid;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Block\BlockInterface;

/**
 * Filling Product type layout.
 */
class Products extends LayoutForm
{
    /**
     * Product grid block.
     *
     * @var string
     */
    protected $productGrid = '//*[@class="chooser_container"]';

    /**
     * Filling layout product form.
     *
     * @param array $widgetOptionsFields
     * @param Element $element [optional]
     * @return void
     */
    public function fillForm(array $widgetOptionsFields, Element $element = null)
    {
        $mapping = $this->dataMapping($widgetOptionsFields);
        $fields = array_diff_key($mapping, ['entities' => '']);
        foreach ($fields as $key => $field) {
            $this->_fill([$key => $field], $this->_rootElement);
            $this->getTemplateBlock()->waitLoader();
        }
        if (isset($mapping['entities'])) {
            $this->selectEntitiesInGrid($mapping['entities']);
        }
    }

    /**
     * Select entities in grid on layout tab.
     *
     * @param array $entities
     * @return void
     */
    protected function selectEntitiesInGrid(array $entities)
    {
        foreach($entities['value'] as $entity) {
            $this->_rootElement->find($this->chooser, Locator::SELECTOR_XPATH)->click();
            $this->getTemplateBlock()->waitLoader();
            /** @var Grid $productGrid */
            $productGrid = $this->getProductGrid();
            $productGrid->searchAndSelect(['name' => $entity['name']]);
            $this->getTemplateBlock()->waitLoader();
            $this->_rootElement->find($this->apply, Locator::SELECTOR_XPATH)->click();
        }
    }

    /**
     * Get product grid.
     *
     * @return BlockInterface
     */
    protected function getProductGrid()
    {
        return $this->blockFactory->create(
            'Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\Product\Grid',
            [
                'element' => $this->_rootElement->find($this->productGrid, Locator::SELECTOR_XPATH)
            ]
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
        $dataMapping = $this->dataMapping($fields);
        $data = $this->_getData($dataMapping, $element);
        if (isset($dataMapping['entities'])) {
            $data['entities'] = $this->getProducts($dataMapping['entities']['value']);
        }
        return $data;
    }

    /**
     * Get products.
     *
     * @param array $products
     * @return array
     */
    protected function getProducts(array $products)
    {
        $result = [];
        /** @var Grid $productGrid */
        $productGrid = $this->getProductGrid();
        foreach ($products as $key => $product) {
            $this->_rootElement->find($this->chooser, Locator::SELECTOR_XPATH)->click();
            $this->getTemplateBlock()->waitLoader();
            if ($productGrid->isSelect(['sku' => $product['sku']])) {
                $result[$key]['name'] = $product['name'];
            }
        }
        return $result;
    }
}
