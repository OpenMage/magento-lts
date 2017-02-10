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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Widget\Test\Block;

use Mage\Widget\Test\Fixture\Widget;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Widget block on the frontend.
 */
class WidgetView extends Block
{
    /**
     * Widgets selectors.
     *
     * @var array
     */
    protected $widgetSelectors = [];

    /**
     * Check widget.
     *
     * @param Widget $widget
     * @param string $pageName
     * @return array
     * @throws \Exception
     */
    public function checkWidget(Widget $widget, $pageName)
    {
        $error = [];
        $widgetType = $widget->getWidgetOptions()['type_id'];
        if ($this->hasRender($widgetType)) {
            return $this->callRender($widgetType, 'checkWidget', ['widget' => $widget, 'pageName' => $pageName]);
        } elseif (isset($this->widgetSelectors[$widgetType])) {
            $widgetOptions = $widget->getWidgetOptions();
            unset($widgetOptions['type_id']);
            foreach ($widgetOptions as $widgetOption) {
                $error[] = array_filter($this->checkEntities($widget, $widgetOption, $pageName, $widgetType));
            }
            return array_filter($error);
        } else {
            throw new \Exception('Determine how to find the widget on the page.');
        }
    }

    /**
     * Check widget entities.
     *
     * @param Widget $widget
     * @param array $widgetOption
     * @param string $pageName
     * @param string $widgetType
     * @return array
     */
    protected function checkEntities(Widget $widget, $widgetOption, $pageName, $widgetType)
    {
        $error = [];
        if (isset($widgetOption['entities'])) {
            foreach ($widgetOption['entities'] as $entity) {
                $widgetText = $entity->getStoreContents()['store_content'];
                $visibility = $this->isWidgetVisible($widget, $pageName, $widgetType, $widgetText);
                if ($visibility !== null) {
                    $error[] = $visibility;
                }
            }
        } else {
            $error[] = $this->isWidgetVisible($widget, $pageName, $widgetType, $pageName);
        }

        return $error;
    }

    /**
     * Check is visible widget selector.
     *
     * @param Widget $widget
     * @param string $pageName
     * @param string $widgetType
     * @param string $widgetText
     * @return string|null
     */
    protected function isWidgetVisible(Widget $widget, $pageName, $widgetType, $widgetText)
    {
        $widgetSelector = sprintf($this->widgetSelectors[$widgetType], $widgetText);
        return $this->_rootElement->find($widgetSelector, Locator::SELECTOR_XPATH)->isVisible()
            ? null
            : "Widget with title {$widget->getTitle()} is absent on {$pageName}  page.";
    }

    /**
     * Click to widget selector.
     *
     * @param Widget $widget
     * @param string $widgetText
     * @return void
     * @throws \Exception
     */
    public function clickToWidget(Widget $widget, $widgetText)
    {
        $widgetType = $widget->getWidgetOptions()['type_id'];
        if ($this->hasRender($widgetType)) {
            $this->callRender($widgetType, 'clickToWidget', ['widget' => $widget, 'widgetText' => $widgetText]);
        } elseif (isset($this->widgetSelectors[$widgetType])) {
            $this->_rootElement->find(
                sprintf($this->widgetSelectors[$widgetType], $widgetText),
                Locator::SELECTOR_XPATH
            )->click();
        } else {
            throw new \Exception('Determine how to find the widget on the page.');
        }
    }
}
