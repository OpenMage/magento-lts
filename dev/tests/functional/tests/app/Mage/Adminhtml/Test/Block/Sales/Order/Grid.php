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

namespace Mage\Adminhtml\Test\Block\Sales\Order;

/**
 * Sales order grid.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * Order Id td selector.
     *
     * @var string
     */
    protected $editLink = 'a[data-column="action"]';

    /**
     * First row selector.
     *
     * @var string
     */
    protected $firstRowSelector = '//tr[./td[contains(@class,"last")]][1]//a';

    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'id' => [
            'selector' => 'input[name="real_order_id"]',
        ],
        'status' => [
            'selector' => 'select[name="status"]',
            'input' => 'select',
        ],
        'purchased_from' => [
            'selector' => 'select[name="store_id"]',
            'input' => 'select',
        ],
    ];
}
