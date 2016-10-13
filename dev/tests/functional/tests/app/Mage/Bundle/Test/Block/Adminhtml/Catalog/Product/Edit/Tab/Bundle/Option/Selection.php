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

namespace Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

use Magento\Mtf\Block\Form;

/**
 * Assigned product row to bundle option.
 */
class Selection extends Form
{
    /**
     * Data for unset as non-using in mapping
     *
     * @var array
     */
    protected $unsetElementsData = ['sku', 'name'];
    /**
     * Fill data to product row.
     *
     * @param array $fields
     * @return void
     */
    public function fillProductRow(array $fields)
    {
        $mapping = $this->dataMapping($this->unsetElements($fields));
        $this->_fill($mapping);
    }

    /**
     * Get data item selection.
     *
     * @param array $fields
     * @return array
     */
    public function getProductRow(array $fields)
    {
        $mapping = $this->dataMapping($this->unsetElements($fields));
        $newFields = $this->_getData($mapping);
        $newFields['sku'] = $this->getProductSku($mapping['getProductSku']);
        $newFields['name'] = $this->getProductName($mapping['getProductName']);
        unset($newFields['getProductSku'], $newFields['getProductName']);

        return $newFields;
    }

    /**
     * Get product SKU.
     *
     * @param array $skuField
     * @return string
     */
    protected function getProductSku(array $skuField)
    {
        $productSku = $this->_rootElement->find($skuField['selector'], $skuField['strategy'])->getText();
        return preg_replace('@SKU: (.*)@', '$1', $productSku);
    }

    /**
     * Get product name.
     *
     * @param array $nameField
     * @return string
     */
    protected function getProductName(array $nameField)
    {
        $productName = $this->_rootElement->find($nameField['selector'], $nameField['strategy'])->getText();
        preg_match('@(.*)\n@', $productName, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }

    protected function unsetElements(array $fields)
    {
        foreach($this->unsetElementsData as $value) {
            unset($fields[$value]);
        }

        return $fields;
    }
}
