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

namespace Mage\Adminhtml\Test\Block\Catalog\Category\Edit\Tab\Product;

use Magento\Mtf\Client\Locator;

/**
 * Products' grid of Category Products tab.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * 'Select All' link.
     *
     * @var string
     */
    protected $selectAll = '.headings input.checkbox';

    /**
     * An element locator which allows to select entities in grid.
     *
     * @var string
     */
    protected $selectedItem = '.even .checkbox';

    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'sku' => [
            'selector' => '#catalog_category_products_filter_sku'
        ]
    ];

    /**
     * Clear grid.
     *
     * @return void
     */
    public function clear()
    {
        $this->_rootElement->find($this->selectAll, Locator::SELECTOR_CSS, 'checkbox')->setValue('No');
    }

    /**
     * Search for item product and select it.
     *
     * @param array $filter
     * @return void
     * @throws \Exception
     */
    public function searchAndSelect(array $filter)
    {
        $this->search($filter);
        $selectItem = $this->_rootElement->find($this->selectItem, Locator::SELECTOR_CSS, 'checkbox');
        if ($selectItem->isVisible()) {
            $selectItem->setValue('No');
            $selectItem->setValue('Yes');
        } else {
            throw new \Exception('Searched item product was not found.');
        }
    }
}
