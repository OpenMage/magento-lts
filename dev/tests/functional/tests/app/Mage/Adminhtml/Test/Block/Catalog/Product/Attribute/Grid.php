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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Attribute;

/**
 * Attribute grid on the product attribute index page.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * Locator value for link in action column.
     *
     * @var string
     */
    protected $editLink = 'td.last';

    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'attribute_code' => [
            'selector' => 'input[name="attribute_code"]'
        ],
        'frontend_label' => [
            'selector' => 'input[name="frontend_label"]'
        ],
        'is_required' => [
            'selector' => 'select[name="is_required"]',
            'input' => 'select'
        ],
        'is_user_defined' => [
            'selector' => 'select[name="is_user_defined"]',
            'input' => 'select'
        ],
        'is_visible' => [
            'selector' => 'select[name="is_visible"]',
            'input' => 'select'
        ],
        'is_global' => [
            'selector' => 'select[name="is_global"]',
            'input' => 'select'
        ],
        'is_searchable' => [
            'selector' => 'select[name="is_searchable"]',
            'input' => 'select'
        ],
        'is_filterable' => [
            'selector' => 'select[name="is_filterable"]',
            'input' => 'select'
        ],
        'is_comparable' => [
            'selector' => 'select[name="is_comparable"]',
            'input' => 'select'
        ]
    ];
}
