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

namespace Mage\Adminhtml\Test\Block\Catalog\Product;

/**
 * Backend catalog product grid.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * An element locator which allows to select entities in grid.
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .massaction-checkbox';

    /**
     * Locator value for link in action column.
     *
     * @var string
     */
    protected $editLink = 'td[class*=last] a';

    /**
     * Initialize block elements.
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '#productGrid_product_filter_name'
        ],
        'sku' => [
            'selector' => '#productGrid_product_filter_sku'
        ],
        'type' => [
            'selector' => '#productGrid_product_filter_type',
            'input' => 'select'
        ],
        'price_from' => [
            'selector' => '#productGrid_product_filter_price_from'
        ],
        'price_to' => [
            'selector' => '#productGrid_product_filter_price_to'
        ],
        'qty_from' => [
            'selector' => '#productGrid_product_filter_qty_from'
        ],
        'qty_to' => [
            'selector' => '#productGrid_product_filter_qty_to'
        ],
        'visibility' => [
            'selector' => '#productGrid_product_filter_visibility',
            'input' => 'select'
        ],
        'status' => [
            'selector' => '#productGrid_product_filter_status',
            'input' => 'select'
        ],
        'set_name' => [
            'selector' => '#productGrid_product_filter_set_name',
            'input' => 'select'
        ]
    ];
}
