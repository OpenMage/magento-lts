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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Widget\Tab;

/**
 * Product custom options tab.
 */
class CustomOptions extends Tab
{
    /**
     * Css selector for 'Add New Option' button.
     *
     * @var string
     */
    protected $addOptions = '#add_new_defined_option';

    /**
     * Item custom options css selector.
     *
     * @var string
     */
    protected $itemCustomOptions = '//div[@class="option-box"][%d]';

    /**
     * Custom sub options scc selector.
     *
     * @var string
     */
    protected $subOptions = '.grid.form-list';

    /**
     * Checked option type selector.
     *
     * @var string
     */
    protected $checkedOptionType = '.select-product-option-type :checked';

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $context = $element ? $element : $this->_rootElement;
        foreach ($fields['custom_options']['value'] as $field) {
            $context->find($this->addOptions)->click();
            $element = $this->getOptionsBlock(1, $context);
            $options = array_pop($field);
            $data = $this->dataMapping($field);
            $this->_fill($data, $element);
            $field['type'] = explode('/', $field['type'])[1];
            $this->fillSubOptions([$field['type'] => $options], $element);
        }

        return $this;
    }

    /**
     * Fill price options.
     *
     * @param array $options
     * @param Element $element
     * @return void
     */
    protected function fillSubOptions(array $options, Element $element)
    {
        $optionType = key($options);
        $this->getSubOptionsBlock($optionType, $element)->fillOptions($options[$optionType]);
    }

    /**
     * Get option name.
     *
     * @param string $optionType
     * @return string
     */
    protected function getOptionName($optionType)
    {
        $optionName = str_replace([' ', '&'], "", $optionType);
        $end = strpos($optionType, '-');
        if ($end !== false) {
            $optionName = substr($optionName, 0, $end) . ucfirst(substr($optionName, ($end + 1)));
        }
        return $optionName;
    }

    /**
     * Get sub options block.
     *
     * @param string $optionType
     * @param Element $context
     * @return AbstractOptions
     */
    protected function getSubOptionsBlock($optionType, Element $context)
    {
        return $this->blockFactory->create(
            __NAMESPACE__ . '\\CustomOptions\\' . $this->getOptionName($optionType),
            ['element' => $context->find($this->subOptions)]
        );
    }

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $result = [];
        $context = $element ? $element : $this->_rootElement;
        foreach ($fields['custom_options']['value'] as $key => $field) {
            $element = $this->getOptionsBlock(($key + 1), $context);
            $data = $this->dataMapping($field);
            unset($data['options']);
            $result['custom_options'][$key] = $this->_getData($data, $element);
            $result['custom_options'][$key]['options'] = $this->getSubOptions($element);
        }

        return $result;
    }

    /**
     * Get sub options.
     *
     * @param Element $element
     * @return array
     */
    protected function getSubOptions(Element $element)
    {
        $optionType = $element->find($this->checkedOptionType)->getText();
        $optionType = str_replace([' ', '&', '-'], '', $optionType);
        return $this->getSubOptionsBlock($optionType, $element)->getOptions();
    }

    /**
     * Get sub options.
     *
     * @param Element $element
     * @param int $key
     * @return Element
     */
    protected function getOptionsBlock($key, Element $element)
    {
        $element = $element ? $element : $this->_rootElement;
        return $element->find(sprintf($this->itemCustomOptions, $key), Locator::SELECTOR_XPATH);
    }
}
