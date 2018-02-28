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

namespace Mage\Bundle\Test\Block\Catalog\Product\View\Type;

use Mage\Bundle\Test\Fixture\BundleProduct;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Catalog bundle product info block.
 */
class Bundle extends Block
{
    /**
     * Selector for title of option.
     *
     * @var string
     */
    protected $title = '//dt/label';

    /**
     * Bundle option locator.
     *
     * @var string
     */
    protected $optionElement = '//dd[%d]/div';

    /**
     * Selector for required option.
     *
     * @var string
     */
    protected $required = '//preceding-sibling::dt[1]/label[@class="required"]';

    /**
     * Selector for select element of option.
     *
     * @var string
     */
    protected $selectOption = './select';

    /**
     * Selector for label of option value element.
     *
     * @var string
     */
    protected $optionLabel = './*[contains(@class,"options-list")]//li';

    /**
     * Selector for option of select element.
     *
     * @var string
     */
    protected $option = './option[%d]';

    /**
     * Selector bundle option form for fill.
     *
     * @var string
     */
    protected $bundleOptionForm = './/dd[preceding-sibling::dt[1][contains(.,"%s")]]';

    /**
     * Get product options.
     *
     * @param InjectableFixture $product
     * @return array
     * @throws \Exception
     */
    public function getOptions(InjectableFixture $product)
    {
        /** @var BundleProduct $product */
        $bundleSelections = $product->getBundleSelections();
        $listFormOptions = $this->getListOptions();
        $formOptions = [];

        foreach ($bundleSelections as $option) {
            $optionData = $this->prepareOption($listFormOptions, $option);
            $getTypeData = 'get' . $this->optionNameConvert($option['type']) . 'Data';
            $formOptions[] = array_merge($optionData, $this->$getTypeData($listFormOptions[$option['title']]));
        }

        return $formOptions;
    }

    /**
     * Prepare option data.
     *
     * @param array $listFormOptions
     * @param array $fixtureOption
     * @return array
     * @throws \Exception
     */
    protected function prepareOption(array $listFormOptions, array $fixtureOption)
    {
        $title = $fixtureOption['title'];
        if (!isset($listFormOptions[$title])) {
            throw new \Exception("Can't find option: \"{$title}\"");
        }
        /** @var Element $optionElement */
        $optionElement = $listFormOptions[$title];
        $optionData = [
            'title' => $title,
            'type' => $fixtureOption['type'],
            'is_require' => $this->checkRequireOption($optionElement)
        ];

        return $optionData;
    }

    /**
     * Check option is require or not.
     *
     * @param Element $optionElement
     * @return string
     */
    protected function checkRequireOption(Element $optionElement)
    {
        return $optionElement->find($this->required, Locator::SELECTOR_XPATH)->isVisible() ? 'Yes' : 'No';
    }

    /**
     * Fill bundle options.
     *
     * @param array $bundleOptions
     * @return void
     */
    public function fillBundleOptions($bundleOptions)
    {
        foreach ($bundleOptions as $option) {
            $this->getItemOptionForm($option)->fillOption($option['value']);
        }
    }

    /**
     * Get item option form.
     *
     * @param array $option
     * @return Option
     */
    protected function getItemOptionForm(array $option)
    {
        $selector = sprintf($this->bundleOptionForm, $option['title']);
        return $this->blockFactory->create(
            'Mage\Bundle\Test\Block\Catalog\Product\View\Type\Option\\' . $this->optionNameConvert($option['type']),
            ['element' => $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get list options.
     *
     * @return array
     */
    protected function getListOptions()
    {
        $options = [];
        $titles = $this->_rootElement->getElements($this->title, Locator::SELECTOR_XPATH);
        foreach ($titles as $key => $title) {
            $options[$title->getText()] = $this->_rootElement->find(
                sprintf($this->optionElement, $key + 1),
                Locator::SELECTOR_XPATH
            );
        }

        return $options;
    }

    /**
     * Get data of "Drop-down" option.
     *
     * @param Element $option
     * @return array
     */
    protected function getDropdownData(Element $option)
    {
        $select = $option->find($this->selectOption, Locator::SELECTOR_XPATH, 'select');
        // Skip "Choose a selection ..."(option #1)
        return $this->getSelectOptionsData($select, 2);
    }

    /**
     * Get data of "Multiple select" option.
     *
     * @param Element $option
     * @return array
     */
    protected function getMultipleselectData(Element $option)
    {
        $multiselect = $option->find($this->selectOption, Locator::SELECTOR_XPATH, 'multiselect');
        $data = $this->getSelectOptionsData($multiselect, 1);

        foreach ($data['options'] as $key => $option) {
            $option['title'] = trim(preg_replace('/^[\d]+ x/', '', $option['title']));
            $data['options'][$key] = $option;
        }

        return $data;
    }

    /**
     * Get data of "Radio buttons" option.
     *
     * @param Element $option
     * @return array
     */
    protected function getRadiobuttonsData(Element $option)
    {
        $listOptions = [];
        $optionLabels = $option->getElements($this->optionLabel, Locator::SELECTOR_XPATH);

        foreach ($optionLabels as $optionLabel) {
            if ($optionLabel->isVisible()) {
                $listOptions[] = $this->parseOptionText($optionLabel->getText());
            }
        }

        return ['options' => $listOptions];
    }

    /**
     * Get data of "Checkbox" option.
     *
     * @param Element $option
     * @return array
     */
    protected function getCheckboxData(Element $option)
    {
        $data = $this->getRadiobuttonsData($option);

        foreach ($data['options'] as $key => $option) {
            $option['title'] = trim(preg_replace('/^[\d]+ x/', '', $option['title']));
            $data['options'][$key] = $option;
        }

        return $data;
    }

    /**
     * Get data from option of select and multiselect.
     *
     * @param Element $element
     * @param int $firstOption
     * @return array
     */
    protected function getSelectOptionsData(Element $element, $firstOption = 1)
    {
        $listOptions = [];

        $count = $firstOption;
        $selectOption = $element->find(sprintf($this->option, $count), Locator::SELECTOR_XPATH);
        while ($selectOption->isVisible()) {
            $listOptions[] = $this->parseOptionText($selectOption->getText());
            ++$count;
            $selectOption = $element->find(sprintf($this->option, $count), Locator::SELECTOR_XPATH);
        }

        return ['options' => $listOptions];
    }

    /**
     * Parse option text to title and price.
     *
     * @param string $optionText
     * @return array
     */
    protected function parseOptionText($optionText)
    {
        preg_match('`^(.*?)\+ ?\$(\d.*?)$`', $optionText, $match);
        $optionPrice = isset($match[2]) ? str_replace(',', '', $match[2]) : 0;
        $optionTitle = isset($match[1]) ? trim($match[1]) : $optionText;

        return [
            'title' => $optionTitle,
            'price' => $optionPrice
        ];
    }

    /**
     * Convert option name.
     *
     * @param string $optionType
     * @return string
     */
    protected function optionNameConvert($optionType)
    {
        $trimmedOptionType = preg_replace('/[^a-zA-Z]/', '', $optionType);
        return ucfirst(strtolower($trimmedOptionType));
    }
}
