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

namespace Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\LayoutForm;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Template;
use Magento\Mtf\Block\BlockInterface;

/**
 * Layout Updates tab.
 */
class LayoutUpdates extends Tab
{
    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Form selector.
     *
     * @var string
     */
    protected $formSelector = './/div[contains(@id,"page_group_container_%d")]';

    /**
     * 'Add Option' button.
     *
     * @var string
     */
    protected $addLayoutUpdates = 'button.scalable.add';

    /**
     * Fill Layout Updates form.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields['layout']['value'] as $key => $field) {
            $this->addLayoutUpdates();
            /** @var LayoutForm $layoutForm */
            $layoutForm = $this->getLayoutForm($field['page_group'], $key);
            $layoutForm->fillForm($field);
        }
        return $this;
    }

    /**
     * Get layout form.
     *
     * @param string $field
     * @param string $key
     *
     * @return BlockInterface
     */
    protected function getLayoutForm($field, $key)
    {
        $path = 'Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\\';
        $pageGroup = explode('/', $field);
        /** @var LayoutForm $layoutForm */
        return $this->blockFactory->create(
            $path . str_replace(" ", "", $pageGroup[0]),
            ['element' => $this->_rootElement->find(sprintf($this->formSelector, $key), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Click Add Layout Updates button.
     *
     * @return void
     */
    protected function addLayoutUpdates()
    {
        $this->_rootElement->find($this->addLayoutUpdates)->click();
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

    /**
     * Get data of content tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array|null
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $data = [];
        foreach ($fields['layout'] as $key => $layout) {
            $layoutForm = $this->getLayoutForm($layout['page_group'], $key);
            $data['layout'][] = $layoutForm->getDataFormTab($layout, $element);
        }

        return $data;
    }
}
