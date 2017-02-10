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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable;

use Magento\Mtf\Client\Locator;

/**
 * Configurable product grid.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * An element locator which allows to select entities in grid.
     *
     * @var string
     */
    protected $selectedItem = 'tbody tr .checkbox';

    /**
     * 'Select All' link.
     *
     * @var string
     */
    protected $selectAll = 'thead input.checkbox';

    /**
     * Initialize block elements.
     *
     * @var array
     */
    protected $filters = [
        'sku' => [
            'selector' => '#super_product_links_filter_sku'
        ]
    ];

    /**
     * Unselect all products.
     *
     * @return void
     */
    public function unselectAllProducts()
    {
        $this->_rootElement->find($this->selectAll, Locator::SELECTOR_CSS, 'checkbox')->setValue('No');
    }
}
