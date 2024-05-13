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

namespace Mage\Adminhtml\Test\Block\CatalogSearch;

/**
 * Search terms grid.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * Initialize block elements.
     *
     * @var array
     */
    protected $filters = [
        'search_query' => [
            'selector' => 'input[name="search_query"]',
        ],
        'store_id' => [
            'selector' => 'select[name="store_id"]',
            'input' => 'selectstore',
        ],
        'results_from' => [
            'selector' => 'input[name="num_results[from]"]',
        ],
        'popularity_from' => [
            'selector' => 'input[name="popularity[from]"]',
        ],
        'synonym_for' => [
            'selector' => 'input[name="synonym_for"]',
        ],
        'redirect' => [
            'selector' => 'input[name="redirect"]',
        ],
        'display_in_terms' => [
            'selector' => 'select[name="display_in_terms"]',
            'input' => 'select',
        ],
    ];
}
