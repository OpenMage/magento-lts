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

namespace Mage\Adminhtml\Test\Block\Review;

/**
 * Reviews grid.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'review_id' => [
            'selector' => 'input[name="review_id"]',
        ],
        'title' => [
            'selector' => 'input[name="title"]',
        ],
        'status_id' => [
            'selector' => '#reviwGrid_filter_status',
            'input' => 'select',
        ],
        'nickname' => [
            'selector' => 'input[name="nickname"]',
        ],
        'detail' => [
            'selector' => 'input[name="detail"]',
        ],
        'type' => [
            'selector' => 'select[name="type"]',
            'input' => 'select',
        ],
        'name' => [
            'selector' => 'input[name="name"]',
        ],
        'sku' => [
            'selector' => 'input[name="sku"]',
        ],
    ];
}
