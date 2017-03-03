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

namespace Mage\Catalog\Test\Block\Product\View;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Form of custom options product.
 */
class CustomOptions extends Form
{
    /**
     * Selector for options context.
     *
     * @var string
     */
    protected $optionsContext = '//div[@id="product-options-wrapper"]/dl[%d]';

    /**
     * Selector for single option title block.
     *
     * @var string
     */
    protected $optionElementTitle = './/dt[%d]';

    /**
     * Selector for single option block.
     *
     * @var string
     */
    protected $optionElement = './/dd[%d]';

    /**
     * Selector for title of option.
     *
     * @var string
     */
    protected $title = 'label';

    /**
     * Selector for required option.
     *
     * @var string
     */
    protected $required = './/label[contains(@class,"required")]';

    /**
     * Selector for price notice of option.
     *
     * @var string
     */
    protected $priceNotice = './/*[@class="price-notice"]';

    /**
     * Selector for max characters of option.
     *
     * @var string
     */
    protected $maxCharacters = './/p[@class="note"]/strong';

    /**
     * Selector for label of option value element.
     *
     * @var string
     */
    protected $optionLabel = './/label[contains(@for, "options_")][%d]';

    /**
     * Select note of option by number.
     *
     * @var string
     */
    protected $noteByNumber = './/*[@class="no-margin"][%d]/strong';

    /**
     * Selector for select element of option.
     *
     * @var string
     */
    protected $selectOption = './/div[@class="input-box"]/select';

    /**
     * Selector for option of select element.
     *
     * @var string
     */
    protected $option = './/option[%d]';

    /**
     * Option XPath locator by value.
     *
     * @var string
     */
    protected $optionByValueLocator = '//*[@id="product-options-wrapper"]//option[text()="%s"]/..';

    /**
     * Select XPath locator by title.
     *
     * @var string
     */
    protected $selectByTitleLocator = '//*[*[@id="product-options-wrapper"]//span[text()="%s"]]//select';

    /**
     * Select XPath locator by option name.
     *
     * @var string
     */
    protected $optionByName = '//*[@id="product-options-wrapper"]/dl[.//label[contains(.,"%s")]]';

    /**
     * Get product options.
     *
     * @param InjectableFixture $product
     * @return array
     * @throws \Exception
     */
    public function getOptions(InjectableFixture $product)
    {
        $dataOptions = $product->hasData('custom_options') ? $product->getCustomOptions() : [];
        if (empty($dataOptions)) {
            return $dataOptions;
        }
        $listCustomOptions = $this->getListOptions();
        $result = [];

        foreach ($dataOptions as $option) {
            $title = $option['title'];
            if (!isset($listCustomOptions[$title])) {
                throw new \Exception("Can't find option: \"{$title}\"");
            }

            /** @var Element $optionElement */
            $optionElement = $listCustomOptions[$title];
            $option['type'] = explode('/', $option['type'])[1];
            $typeMethod = preg_replace('/[^a-zA-Z]/', '', $option['type']);
            $getTypeData = 'get' . ucfirst(strtolower($typeMethod)) . 'Data';

            $optionData = $this->$getTypeData($optionElement);
            $optionData['title'] = $title;
            $optionData['type'] = $option['type'];
            $optionData['is_require'] = $optionElement['title']->find($this->required, Locator::SELECTOR_XPATH)
                ->isVisible()
                ? 'Yes'
                : 'No';

            $result[$title] = $optionData;
        }

        return ['custom_options' => $result];
    }

    /**
     * Get list custom options.
     *
     * @return array
     */
    protected function getListOptions()
    {
        $customOptions = [];
        $context = $this->getOptionsContext();
        $count = 1;
        $optionElementTitle = $context->find(sprintf($this->optionElementTitle, $count), Locator::SELECTOR_XPATH);
        $optionElement = $context->find(sprintf($this->optionElement, $count), Locator::SELECTOR_XPATH);
        while ($optionElementTitle->isVisible()) {
            $title = $optionElementTitle->find($this->title)->getText();
            $customOptions[$title]['title'] = $optionElementTitle;
            $customOptions[$title]['content'] = $optionElement;
            ++$count;
            $optionElementTitle = $context->find(sprintf($this->optionElementTitle, $count), Locator::SELECTOR_XPATH);
            $optionElement = $context->find(sprintf($this->optionElement, $count), Locator::SELECTOR_XPATH);
        }
        return $customOptions;
    }

    /**
     * Get options context.
     *
     * @return Element
     */
    protected function getOptionsContext()
    {
        return $this->_rootElement->find(sprintf($this->optionsContext, 2), Locator::SELECTOR_XPATH)->isVisible()
            ? $this->_rootElement->find(sprintf($this->optionsContext, 2), Locator::SELECTOR_XPATH)
            : $this->_rootElement->find(sprintf($this->optionsContext, 1), Locator::SELECTOR_XPATH);
    }

    /**
     * Get data of "Field" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getFieldData(array $option)
    {
        $price = $this->getOptionPriceNotice($option['title']);
        $maxCharacters = $option['content']->find($this->maxCharacters, Locator::SELECTOR_XPATH);
        return [
            'options' => [
                'price' => floatval($price),
                'max_characters' => $maxCharacters->isVisible() ? $maxCharacters->getText() : null,
            ]
        ];
    }

    /**
     * Get data of "Area" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getAreaData(array $option)
    {
        return $this->getFieldData($option);
    }

    /**
     * Get data of "File" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getFileData(array $option)
    {
        $price = $this->getOptionPriceNotice($option['title']);

        return [
            'options' => [
                'price' => floatval($price),
                'file_extension' => $this->getOptionNotice($option['content'], 1),
                'image_size_x' => preg_replace('/[^0-9]/', '', $this->getOptionNotice($option['content'], 2)),
                'image_size_y' => preg_replace('/[^0-9]/', '', $this->getOptionNotice($option['content'], 3)),
            ]
        ];
    }

    /**
     * Get data of "Drop-down" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getDropdownData(array $option)
    {
        $select = $option['content']->find($this->selectOption, Locator::SELECTOR_XPATH, 'select');
        // Skip "Choose option ..."(option #1)
        return $this->getSelectOptionsData($select, 2);
    }

    /**
     * Get data of "Multiple Select" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getMultipleSelectData(array $option)
    {
        $multiSelect = $option['content']->find($this->selectOption, Locator::SELECTOR_XPATH, 'multiselect');
        return $this->getSelectOptionsData($multiSelect, 1);
    }

    /**
     * Get data of "Radio Buttons" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getRadioButtonsData(array $option)
    {
        $listOptions = [];

        $count = 1;
        $optionLabel = $option['content']->find(sprintf($this->optionLabel, $count), Locator::SELECTOR_XPATH);
        while ($optionLabel->isVisible()) {
            $listOptions[] = $this->parseOptionText($optionLabel->getText());
            ++$count;
            $optionLabel = $option['content']->find(sprintf($this->optionLabel, $count), Locator::SELECTOR_XPATH);
        }

        return [
            'options' => $listOptions
        ];
    }

    /**
     * Get data of "Checkbox" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getCheckboxData(array $option)
    {
        return $this->getRadioButtonsData($option);
    }

    /**
     * Get data of "Date" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getDateData(array $option)
    {
        $price = $this->getOptionPriceNotice($option['title']);

        return [
            'options' => [
                'price' => floatval($price)
            ]
        ];
    }

    /**
     * Get data of "Date & Time" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getDateTimeData(array $option)
    {
        return $this->getDateData($option);
    }

    /**
     * Get data of "Time" custom option.
     *
     * @param array $option
     * @return array
     */
    protected function getTimeData(array $option)
    {
        return $this->getDateData($option);
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
     * Get price from price-notice of custom option.
     *
     * @param Element $option
     * @return array
     */
    protected function getOptionPriceNotice(Element $option)
    {
        $priceNotice = $option->find($this->priceNotice, Locator::SELECTOR_XPATH);
        if (!$priceNotice->isVisible()) {
            return null;
        }
        return preg_replace('/[^0-9\.]/', '', $priceNotice->getText());
    }

    /**
     * Get notice of option by number.
     *
     * @param Element $option
     * @param int $number
     * @return mixed
     */
    protected function getOptionNotice(Element $option, $number)
    {
        $note = $option->find(sprintf($this->noteByNumber, $number), Locator::SELECTOR_XPATH);
        return $note->isVisible() ? $note->getText() : null;
    }

    /**
     * Parse option text to title and price.
     *
     * @param string $optionText
     * @return array
     */
    protected function parseOptionText($optionText)
    {
        preg_match('`^(.*?)\+\$(\d.*?)$`', $optionText, $match);
        $optionPrice = isset($match[2]) ? str_replace(',', '', $match[2]) : 0;
        $optionTitle = isset($match[1]) ? trim($match[1]) : $optionText;

        return [
            'title' => $optionTitle,
            'price' => $optionPrice
        ];
    }

    /**
     * Fill custom options.
     *
     * @param array $checkoutData
     * @return void
     */
    public function fillCustomOptions(array $checkoutData)
    {
        $checkoutOptions = $this->prepareOptions($checkoutData);
        $this->fillOptions($checkoutOptions);
    }

    /**
     * Prepare composite fields in checkout options data.
     *
     * @param array $options
     * @return array
     */
    protected function prepareOptions(array $options)
    {
        $result = [];

        foreach ($options as $key => $option) {
            switch ($option['type']) {
                case 'datetime':
                    list($day, $month, $year, $hour, $minute, $dayPart) = explode('/', $option['value']);
                    $option['value'] = [
                        'day' => $day,
                        'month' => $month,
                        'year' => $year,
                        'hour' => $hour,
                        'minute' => $minute,
                        'day_part' => $dayPart
                    ];
                    break;
                case 'date':
                    list($day, $month, $year) = explode('/', $option['value']);
                    $option['value'] = [
                        'day' => $day,
                        'month' => $month,
                        'year' => $year,
                    ];
                    break;
                case 'time':
                    list($hour, $minute, $dayPart) = explode('/', $option['value']);
                    $option['value'] = [
                        'hour' => $hour,
                        'minute' => $minute,
                        'day_part' => $dayPart
                    ];
                    break;
            }

            $result[$key] = $option;
        }

        return $result;
    }

    /**
     * Fill product options.
     *
     * @param array $options
     * @return void
     */
    public function fillOptions(array $options)
    {
        foreach ($options as $option) {
            $optionBlock = $this->_rootElement->find(
                sprintf($this->optionByName, $option['title']),
                Locator::SELECTOR_XPATH
            );
            $type = $option['type'];
            $mapping = $this->dataMapping([$type => $option['value']]);

            if ('radiobuttons' == $type || 'checkbox' == $type) {
                $mapping[$type]['selector'] = str_replace(
                    '%option_name%',
                    $mapping[$type]['value'],
                    $mapping[$type]['selector']
                );
                $mapping[$type]['value'] = 'Yes';
            }
            $this->_fill($mapping, $optionBlock);
        }
    }
}
