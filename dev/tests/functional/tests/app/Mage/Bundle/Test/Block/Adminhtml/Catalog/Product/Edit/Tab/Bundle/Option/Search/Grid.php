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

namespace Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search;

/**
 * 'Add Products to Bundle Option' grid.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * Selector for 'Add Selected Product(s) to option' button.
     *
     * @var string
     */
    protected $addProducts = '[id^="add_button"]';

    /**
     * An element locator which allows to select entities in grid.
     *
     * @var string
     */
    protected $selectItem = 'input.checkbox';

    /**
     * Filters param for grid.
     *
     * @var array
     */
    protected $filters = [
        'sku' => [
            'selector' => 'input[name=sku]'
        ]
    ];

    /**
     * Press 'Add Selected Product(s) to Option' button.
     *
     * @return void
     */
    public function addProducts()
    {
        $this->_rootElement->find($this->addProducts)->click();
    }
}
