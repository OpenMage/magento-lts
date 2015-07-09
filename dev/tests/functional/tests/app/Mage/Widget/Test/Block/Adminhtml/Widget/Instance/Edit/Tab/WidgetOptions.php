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
