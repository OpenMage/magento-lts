<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Cms\Page\Widget;

use Mage\Adminhtml\Test\Block\Widget\Grid;

/**
 * Backend select page, block grid.
 */
class Chooser extends Grid
{
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'chooser_identifier' => [
            'selector' => 'input[name="chooser_identifier"]',
        ],
    ];

    /**
     * Locator value for link in action column.
     *
     * @var string
     */
    protected $editLink = 'td';
}
