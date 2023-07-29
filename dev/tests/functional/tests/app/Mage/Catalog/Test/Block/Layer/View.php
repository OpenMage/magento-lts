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

namespace Mage\Catalog\Test\Block\Layer;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Layer navigation block.
 */
class View extends Block
{
    /**
     * Filter link by price.
     *
     * @var string
     */
    protected $filterPriceLink = "//*[contains(text(),'%s')]";

    /**
     * Filter link by attribute.
     *
     * @var string
     */
    protected $filterAttributeLink = "//dt[text()='%s']/following-sibling::dd[1]//a[contains(text(),'%s')]";

    /**
     * Attribute option title selector.
     *
     * @var string
     */
    protected $optionTitle = '#narrow-by-list dt';

    /**
     * Select price.
     *
     * @param string $priceLink
     * @return void
     */
    public function selectPrice($priceLink)
    {
        $this->_rootElement->find(sprintf($this->filterPriceLink, $priceLink), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Select attribute.
     *
     * @param array $filter
     * @return void
     */
    public function selectAttribute(array $filter)
    {
        $selector = sprintf($this->filterAttributeLink, $filter['attribute'], $filter['option']);
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get array of available filters.
     *
     * @return array
     */
    public function getFilters()
    {
        $options = $this->_rootElement->getElements($this->optionTitle);
        $data = [];
        foreach ($options as $option) {
            $data[] = $option->getText();
        }
        return $data;
    }
}
