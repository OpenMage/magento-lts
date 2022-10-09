<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Grid;
use Mage\Adminhtml\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Base class for appurtenant products tab.
 */
abstract class AbstractAppurtenant extends Tab
{
    /**
     * Type appurtenant products.
     *
     * @var string
     */
    protected $type = '';

    /**
     * Select related products.
     *
     * @param array $data
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $data, Element $element = null)
    {
        if (isset($data[$this->type]['value'])) {
            $context = $element ? $element : $this->_rootElement;
            $relatedBlock = $this->getGrid($context);

            foreach ($data[$this->type]['value'] as $product) {
                $relatedBlock->searchAndSelect(['sku' => $product['sku']]);
            }
        }

        return $this;
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
        $relatedBlock = $this->getGrid($element);
        $columns = [
            'entity_id' => '.="ID"',
            'name' => '.="Name"',
            'sku' => '.="SKU"',
        ];
        $relatedProducts = $relatedBlock->getRowsData($columns);

        return [$this->type => $relatedProducts];
    }

    /**
     * Return related products grid.
     *
     * @param Element $element
     * @return Grid
     */
    abstract protected function getGrid(Element $element = null);
}
