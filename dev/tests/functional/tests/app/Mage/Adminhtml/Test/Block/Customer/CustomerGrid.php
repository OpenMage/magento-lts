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

namespace Mage\Adminhtml\Test\Block\Customer;

use Mage\Adminhtml\Test\Block\Widget\Grid;

/**
 * Backend customer grid
 */
class CustomerGrid extends Grid
{
    /**
     * Selector for action option select.
     *
     * @var string
     */
    protected $option = '[name="group"]';

    /**
     * The first row in grid. For this moment we suggest that we should strictly define what we are going to search.
     *
     * @var string
     */
    protected $rowItem = 'div.grid tbody tr';

    /**
     * Locator value for link in action column.
     *
     * @var string
     */
    protected $editLink = 'td[class*=last] a';

    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '#customerGrid_filter_name',
        ],
        'email' => [
            'selector' => '#customerGrid_filter_email',
        ],
        'group' => [
            'selector' => '#customerGrid_filter_group',
            'input' => 'select',
        ],
    ];
}
