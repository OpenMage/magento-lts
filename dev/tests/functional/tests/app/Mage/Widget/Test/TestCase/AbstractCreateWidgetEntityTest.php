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

namespace Mage\Widget\Test\TestCase;

use Mage\Widget\Test\Fixture\Widget;
use Mage\Widget\Test\Page\Adminhtml\WidgetInstanceEdit;
use Mage\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;
use Mage\Widget\Test\Page\Adminhtml\WidgetInstanceNew;
use Magento\Mtf\TestCase\Injectable;

/**
 * Test Creation for New Instance of WidgetEntity.
 */
abstract class AbstractCreateWidgetEntityTest extends Injectable
{
    /**
     * WidgetInstanceIndex page.
     *
     * @var WidgetInstanceIndex
     */
    protected $widgetInstanceIndex;

    /**
     * WidgetInstanceNew page.
     *
     * @var WidgetInstanceNew
     */
    protected $widgetInstanceNew;

    /**
     * WidgetInstanceEdit page.
     *
     * @var WidgetInstanceEdit
     */
    protected $widgetInstanceEdit;

    /**
     * Injection data.
     *
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param WidgetInstanceNew $widgetInstanceNew
     * @param WidgetInstanceEdit $widgetInstanceEdit
     * @return void
     */
    public function __inject(
        WidgetInstanceIndex $widgetInstanceIndex,
        WidgetInstanceNew $widgetInstanceNew,
        WidgetInstanceEdit $widgetInstanceEdit
    ) {
        $this->widgetInstanceIndex = $widgetInstanceIndex;
        $this->widgetInstanceNew = $widgetInstanceNew;
        $this->widgetInstanceEdit = $widgetInstanceEdit;
    }

    /**
     * Delete all widgets.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create('Mage\Widget\Test\TestStep\DeleteAllWidgetsStep')->run();
    }
}
