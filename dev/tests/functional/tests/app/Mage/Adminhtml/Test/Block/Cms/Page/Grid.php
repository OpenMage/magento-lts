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

namespace Mage\Adminhtml\Test\Block\Cms\Page;

/**
 * Backend Cms Page grid.
 */
class Grid extends \Mage\Adminhtml\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => '#cmsPageGrid_filter_title'
        ],
        'identifier' => [
            'selector' => '#cmsPageGrid_filter_identifier'
        ],
        'is_active' => [
            'selector' => '#cmsPageGrid_filter_is_active',
            'input' => 'select'
        ]
    ];

    /**
     * Locator value for link in action column.
     *
     * @var string
     */
    protected $editLink = 'td';

    /**
     * Selector for review link.
     *
     * @var string
     */
    protected $reviewLink = 'a';

    /**
     * The number of attempts for click.
     */
    const COUNT = 3;

    /**
     * Search and review.
     *
     * @param $filter
     * @throws \Exception
     * @return void
     */
    public function searchAndReview($filter)
    {
        $this->search($filter);
        $rowItem = $this->_rootElement->find($this->rowItem);
        if ($rowItem->isVisible()) {
            $count = 0;
            $link = $rowItem->find($this->reviewLink);
            do {
                $link->click();
                $this->browser->selectWindow();
                $count++;
            } while ($link->isVisible() && $count < self::COUNT);
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }
}
