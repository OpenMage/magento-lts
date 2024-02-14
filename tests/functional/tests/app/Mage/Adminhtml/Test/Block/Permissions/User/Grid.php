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

namespace Mage\Adminhtml\Test\Block\Permissions\User;

/**
 * User grid on User index page.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * Grid filters' selectors.
     *
     * @var array
     */
    protected $filters = [
        'username' => [
            'selector' => '#permissionsUserGrid_filter_username'
        ],
        'email' => [
            'selector' => '#permissionsUserGrid_filter_email'
        ]
    ];

    /**
     * Locator value of td with username.
     *
     * @var string
     */
    protected $editLink = 'td.last';
}
