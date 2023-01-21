<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\WidgetOptionsForm;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Block\BlockInterface;

/**
 * Widget options tab.
 */
class WidgetOptions extends Tab
{
    /**
     * Form selector.
     *
     * @var string
     */
    protected $formSelector = '.fieldset-wide';

    /**
     * Path for widget options tab.
     *
     * @var string
     */
    protected $path = 'Mage\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptionsType\\';

    /**
     * Fill Widget options form.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $path = $this->path . ucfirst($fields['widgetOptions']['value']['type_id']);
        unset($fields['widgetOptions']['value']['type_id']);
        foreach ($fields['widgetOptions']['value'] as $field) {
            /** @var WidgetOptionsForm $widgetOptionsForm */
            $widgetOptionsForm = $this->getWidgetOptionsForm($path);
            $widgetOptionsForm->fillForm($field);
        }
        return $this;
    }

    /**
     * Get widget options form.
     *
     * @param string $path
     * @return BlockInterface
     */
    protected function getWidgetOptionsForm($path)
    {
        return $this->blockFactory->create(
            $path,
            ['element' => $this->_rootElement->find($this->formSelector)]
        );
    }
}
