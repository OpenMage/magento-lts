<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\AssociatedProducts;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;

/**
 * Assigned product row to grouped option.
 */
class Product extends Form
{
    /**
     * Product id selector.
     *
     * @var string
     */
    protected $productId = ".//td[2]";

    /**
     * Product name selector.
     *
     * @var string
     */
    protected $productName = ".//td[3]";

    /**
     * Fill product options.
     *
     * @param array $value
     * @return void
     */
    public function fillOptions(array $value)
    {
        $mapping = $this->dataMapping($value);
        $this->_fill($mapping);
    }

    /**
     * Get product options.
     *
     * @param array $value
     * @return array
     */
    public function getOptions(array $value)
    {
        $mapping = $this->dataMapping($value);
        $options = $this->_getData($mapping);
        $options['id'] = $this->getProductId();
        $options['name'] = $this->getProductName();
        return $options;
    }

    /**
     * Get product name.
     *
     * @return string
     */
    protected function getProductName()
    {
        return $this->_rootElement->find($this->productName, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get product id.
     *
     * @return string
     */
    protected function getProductId()
    {
        return $this->_rootElement->find($this->productId, Locator::SELECTOR_XPATH)->getText();
    }
}
