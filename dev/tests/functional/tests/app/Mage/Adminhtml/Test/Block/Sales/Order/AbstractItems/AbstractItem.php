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

namespace Mage\Adminhtml\Test\Block\Sales\Order\AbstractItems;

use Mage\Adminhtml\Test\Block\Sales\Order\AbstractForm\Product;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\ElementInterface;

/**
 * Abstract item block on sales type view page.
 */
abstract class AbstractItem extends Product
{
    /**
     * Columns in grid.
     *
     * @var array
     */
    protected $columns = ['product' => ['col_name' => 'Product']];

    /**
     * Field selector.
     *
     * @var string
     */
    protected $fieldSelector = './/td[count(//th[contains(.,"%s")]/preceding-sibling::th)+1]';

    /**
     * Mapping for products fields.
     *
     * @var array
     */
    protected $productFields = [
        'name' => [
            'selector' => '.title'
        ],
        'sku' => [
            'selector' => './/*[*[contains(text(),"SKU")]]',
            'strategy' => Locator::SELECTOR_XPATH,
            'replace' => 'SKU:'
        ],
        'options' => [
            'selector' => '.item-options'
        ]
    ];

    /**
     * Option title selector.
     *
     * @var string
     */
    protected $optionTitleSelector = 'dt';

    /**
     * Get data from item row.
     *
     * @param array $filter [optional]
     * @return array
     */
    public function getFieldsData(array $filter = [])
    {
        $result = [];
        $fields = empty($filter) ? $this->columns : $filter;
        foreach ($fields as $field => $fieldData) {
            $methodName = 'get' . ucfirst($field);
            if (method_exists($this, $methodName)) {
                $result[$field] = $this->$methodName($fieldData['col_name']);
            } else {
                $column = $this->getColumn($fieldData['col_name']);
                if ($column !== null) {
                    $result[$field] = $this->getColumnValue($column, $fieldData);
                }
            }
        }

        return $result;
    }

    /**
     * Get data from product column.
     *
     * @param string $fieldName
     * @return array
     */
    protected function getProduct($fieldName)
    {
        $result = [];
        $dataColumn = $this->getColumn($fieldName);
        foreach ($this->productFields as $key => $mapping) {
            $strategy = isset($mapping['strategy']) ? $mapping['strategy'] : Locator::SELECTOR_CSS;
            $field = $dataColumn->find($mapping['selector'], $strategy);
            if ($field->isVisible()) {
                if ($key == 'options') {
                    $result[$key] = $this->getOptions($dataColumn);
                } else {
                    $result[$key] = $this->getColumnValue($field, $mapping);
                }
            }
        }

        return $result;
    }

    /**
     * Get column value.
     *
     * @param ElementInterface $element
     * @param array $attributes
     * @return string
     */
    protected function getColumnValue(ElementInterface $element, array $attributes = [])
    {
        $search = isset($attributes['replace']) ? $attributes['replace'] : ['$', '%'];
        return trim(str_replace($search, '', $element->getText()));
    }

    /**
     * Get column.
     *
     * @param string $fieldName
     * @return ElementInterface|null
     */
    protected function getColumn($fieldName)
    {
        $column = $this->_rootElement->find($this->getColumnSelector($fieldName), Locator::SELECTOR_XPATH);
        return $column->isVisible() ? $column : null;
    }

    /**
     * Get column selector.
     *
     * @param string $fieldName
     * @return string
     */
    protected function getColumnSelector($fieldName)
    {
        return sprintf($this->fieldSelector, $fieldName);
    }
}
