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

namespace Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle;

use Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid;
use Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Selection;
use Mage\Adminhtml\Test\Block\Template;
use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;

/**
 * Bundle item option block on bundle items tab.
 */
class Option extends Form
{
    /**
     * Selector for product grid.
     *
     * @var string
     */
    protected $searchGridBlock = '[id^="bundle_option_search_"]';

    /**
     * Added product row.
     *
     * @var string
     */
    protected $selectionBlock = './/tr[contains(@id, "bundle_selection_row_")][not(@style="display: none;")][%d]';

    /**
     * Selector for 'Add Selection' button.
     *
     * @var string
     */
    protected $addSelection = '[id$="add_button"]';

    /**
     * Bundle option title.
     *
     * @var string
     */
    protected $title = '[name$="[title]"]';

    /**
     * Backend abstract block selector.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Get grid for assigning products for bundle option.
     *
     * @return Grid
     */
    protected function getSearchGridBlock()
    {
        return $this->blockFactory->create(
            'Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid',
            ['element' => $this->_rootElement->find($this->searchGridBlock)]
        );
    }

    /**
     * Get item selection product block.
     *
     * @param int $rowIndex
     * @return Selection
     */
    protected function getSelectionBlock($rowIndex)
    {
        return $this->blockFactory->create(
            'Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Selection',
            ['element' => $this->_rootElement->find(sprintf($this->selectionBlock, $rowIndex), Locator::SELECTOR_XPATH)]
        );
    }

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
     * Fill bundle option.
     *
     * @param array $fields
     * @return void
     */
    public function fillOption(array $fields)
    {
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping);
        $this->addAssignedProducts($fields['assigned_products']);
    }

    /**
     * Add assigned products.
     *
     * @param array $assignedProducts
     * @return void
     */
    protected function addAssignedProducts(array $assignedProducts)
    {
        foreach ($assignedProducts as $key => $product) {
            $this->addProduct(['sku' => $product['sku']]);
            $this->getSelectionBlock(++$key)->fillProductRow($product);
        }
    }

    /**
     * Add product to option.
     *
     * @param array $filter
     * @return void
     */
    protected function addProduct(array $filter)
    {
        $this->_rootElement->find($this->addSelection)->click();
        $this->getTemplateBlock()->waitLoader();
        $searchBlock = $this->getSearchGridBlock();
        $searchBlock->searchAndSelect($filter);
        $searchBlock->addProducts();
    }

    /**
     * Get data bundle option.
     *
     * @param array $fields
     * @return array
     */
    public function getOptionData(array $fields)
    {
        $mapping = $this->dataMapping($fields);
        $newField = $this->_getData($mapping);
        foreach ($fields['assigned_products'] as $key => $field) {
            $newField['assigned_products'][$key] = $this->getSelectionBlock($key + 1)->getProductRow($field);
        }

        return $newField;
    }
}
