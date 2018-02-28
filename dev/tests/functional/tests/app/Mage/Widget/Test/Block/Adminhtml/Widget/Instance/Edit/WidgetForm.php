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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Adminhtml\Test\Block\Widget\FormTabs;
use Mage\Widget\Test\Fixture\Widget;

/**
 * Widget Instance edit form.
 */
class WidgetForm extends FormTabs
{
    /**
     * Selector for store view field.
     *
     * @var string
     */
    protected $storeView = '#store_ids';

    /**
     * Fill form with tabs.
     *
     * @param FixtureInterface $widget
     * @param Element|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $widget, Element $element = null)
    {
        $tabs = $this->getFieldsByTabs($widget);
        $this->fillTabs(['settings' => $tabs['settings']]);
        unset($tabs['settings']);
        $this->fillStoreView($widget);
        return $this->fillTabs($tabs, $element);
    }

    /**
     * Fill store view.
     *
     * @param Widget $widget
     * @return void
     */
    protected function fillStoreView(Widget $widget)
    {
        $this->openTab('frontend_properties');
        $storeViewField = $this->_rootElement->find($this->storeView, Locator::SELECTOR_CSS, 'multiselectgrouplist');
        if($storeViewField->isVisible() && !$widget->hasData('store_ids')) {
            $storeViewField->setValue('All Store Views');
        }
    }

    /**
     * Get data of the tabs.
     *
     * @param FixtureInterface|null $fixture
     * @param Element|null $element
     * @return array
     */
    public function getData(FixtureInterface $fixture = null, Element $element = null)
    {
        $widgetType = $fixture->getWidgetOptions()['type_id'];
        if ($this->hasRender($widgetType) && $widgetType != 'bannerRotator') {
            return $this->callRender($widgetType, 'getData', ['InjectableFixture' => $fixture, 'Element' => $element]);
        } elseif ($widgetType == 'bannerRotator') {
            $fixtureData = $fixture->getData();
            unset($fixtureData['widgetOptions'][0]['entities']);
            return $fixtureData;
        }    else {
                return parent::getData($fixture, $element);
        }
    }
}
