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

namespace Mage\Widget\Test\Block\Adminhtml;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Backend add widget form.
 */
class WidgetForm extends Form
{
    /**
     * Widget type selector.
     *
     * @var string
     */
    protected $widgetType = '[name="widget_type"]';

    /**
     * Insert widget button selector.
     *
     * @var string
     */
    protected $insertButton = '#insert_button';

    /**
     * Template block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Add widgets.
     *
     * @param array $widget
     * @return void
     */
    public function addWidget(array $widget)
    {
        $this->selectWidgetType($widget['widget_type']);
        $mapping = $this->dataMapping($widget);
        $this->_fill($mapping);
        $this->insertWidget();
    }

    /**
     * Select widget type.
     *
     * @param string $type
     * @return void
     */
    protected function selectWidgetType($type)
    {
        $this->_rootElement->find($this->widgetType, Locator::SELECTOR_CSS, 'select')->setValue($type);
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Click Insert Widget button.
     *
     * @return void
     */
    protected function insertWidget()
    {
        $this->_rootElement->find($this->insertButton)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Get template block.
     *
     * @return Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
