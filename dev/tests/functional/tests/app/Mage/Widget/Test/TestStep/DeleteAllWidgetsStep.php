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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Widget\Test\TestStep;

use Mage\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Mage\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Delete all widgets on backend.
 */
class DeleteAllWidgetsStep implements TestStepInterface
{
    /**
     * WidgetInstanceIndex page.
     *
     * @var WidgetInstanceIndex
     */
    protected $widgetInstanceIndex;

    /**
     * WidgetInstanceEdit page.
     *
     * @var WidgetInstanceEdit
     */
    protected $widgetInstanceEdit;

    /**
     * @constructor
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param WidgetInstanceEdit $widgetInstanceEdit
     */
    public function __construct(WidgetInstanceIndex $widgetInstanceIndex, WidgetInstanceEdit $widgetInstanceEdit)
    {
        $this->widgetInstanceIndex = $widgetInstanceIndex;
        $this->widgetInstanceEdit = $widgetInstanceEdit;
    }

    /**
     * Delete Widget on backend.
     *
     * @return void
     */
    public function run()
    {
        $this->widgetInstanceIndex->open();
        $this->widgetInstanceIndex->getWidgetGrid()->resetFilter();
        while ($this->widgetInstanceIndex->getWidgetGrid()->isFirstRowVisible()) {
            $this->widgetInstanceIndex->getWidgetGrid()->openFirstRow();
            $this->widgetInstanceEdit->getTemplateBlock()->waitLoader();
            $this->widgetInstanceEdit->getPageActionsBlock()->delete();
        }
    }
}
