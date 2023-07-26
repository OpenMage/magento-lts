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
