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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Cms\Page\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Cms\Wysiwyg\Config;
use Mage\Widget\Test\Block\Adminhtml\WidgetForm;

/**
 * Backend cms page content tab.
 */
class Content extends Tab
{
    /**
     * Page content toolbar selector.
     *
     * @var string
     */
    protected $contentToolBar = "#page_content_toolbargroup";

    /**
     * Hide button selector.
     *
     * @var string
     */
    protected $hideButton = "#togglepage_content";

    /**
     * Insert Variable button selector.
     *
     * @var string
     */
    protected $addVariableButton = ".add-variable";

    /**
     * Insert Widget button selector.
     *
     * @var string
     */
    protected $addWidgetButton = '.add-widget';

    /**
     * System Variable block selector.
     *
     * @var string
     */
    protected $systemVariableBlock = ".//ancestor::body/div[@id='variables-chooser']";

    /**
     * Widget block selector.
     *
     * @var string
     */
    protected $widgetBlock = ".//ancestor::body/div[@id='widget_window']";

    /**
     * Hide editor.
     *
     * @return void
     */
    protected function hideEditor()
    {
        if ($this->_rootElement->find($this->contentToolBar)->isVisible()) {
            $this->_rootElement->find($this->hideButton)->click();
        }
    }

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $this->hideEditor();
        $content = $fields['content']['value'];
        $fields['content']['value'] = $content['content'];
        $data = $this->dataMapping($fields);
        $this->_fill($data, $element);
        if (isset($content['variable'])) {
            $this->clickInsertVariable();
            $config = $this->getWysiwygConfig();
            $config->selectVariable($content['variable']);
        }
        if (isset($content['widget'])) {
            foreach ($content['widget']['preset'] as $widget) {
                $this->clickInsertWidget();
                $this->getWidgetBlock()->addWidget($widget);
            }
        }

        return $this;
    }

    /**
     * Click 'Insert Variable' button.
     *
     * @return void
     */
    public function clickInsertVariable()
    {
        $this->_rootElement->find($this->addVariableButton)->click();
    }

    /**
     * Click 'Insert Widget' button.
     *
     * @return void
     */
    public function clickInsertWidget()
    {
        $this->_rootElement->find($this->addWidgetButton)->click();
    }

    /**
     * Get for wysiwyg config block.
     *
     * @return Config
     */
    public function getWysiwygConfig()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Cms\Wysiwyg\Config',
            ['element' => $this->_rootElement->find($this->systemVariableBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get widget block.
     *
     * @return WidgetForm
     */
    public function getWidgetBlock()
    {
        return $this->blockFactory->create(
            'Mage\Widget\Test\Block\Adminhtml\WidgetForm',
            ['element' => $this->_rootElement->find($this->widgetBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $this->hideEditor();
        return parent::getDataFormTab($fields, $element);
    }
}
