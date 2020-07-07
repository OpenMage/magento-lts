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

namespace Mage\Adminhtml\Test\Block\Tax\Rule;

use Magento\Mtf\Client\Locator;

/**
 * Adminhtml Tax Rules management grid.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * Locator value for opening needed row.
     *
     * @var string
     */
    protected $editLink = 'td';

    /**
     * Initialize block elements.
     *
     * @var array
     */
    protected $filters = [
        'code' => [
            'selector' => '#taxRuleGrid_filter_code',
        ],
        'tax_customer_class' => [
            'selector' => '#taxRuleGrid_filter_customer_tax_classes',
            'input' => 'select',
        ],
        'tax_product_class' => [
            'selector' => '#taxRuleGrid_filter_product_tax_classes',
            'input' => 'select',
        ],
        'tax_rate' => [
            'selector' => '#taxRuleGrid_filter_tax_rates',
            'input' => 'select',
        ],
    ];

    /**
     * Check if specific row exists in grid.
     *
     * @param array $filter
     * @param bool $isSearchable
     * @param bool $isStrict
     * @return bool
     */
    public function isRowVisible(array $filter, $isSearchable = false, $isStrict = true)
    {
        $this->search(['code' => $filter['code']]);
        return parent::isRowVisible($filter, $isSearchable);
    }

    /**
     * Press 'Reset' button.
     *
     * @return void
     */
    public function resetFilter()
    {
        $this->_rootElement->find($this->resetButton, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Search item via grid filter.
     *
     * @param array $filter
     */
    public function search(array $filter)
    {
        $this->resetFilter();
        $this->prepareForSearch($filter);
        $this->_rootElement->find($this->searchButton, Locator::SELECTOR_CSS)->click();
    }
}
