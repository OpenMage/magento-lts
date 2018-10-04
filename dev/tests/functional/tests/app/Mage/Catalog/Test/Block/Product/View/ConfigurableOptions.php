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

namespace Mage\Catalog\Test\Block\Product\View;

use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Form of configurable options product.
 */
class ConfigurableOptions extends CustomOptions
{
    /**
     * Select XPath locator by option name.
     *
     * @var string
     */
    protected $optionByName = '//dt[label[text() = "%s"]]/following-sibling::dd[1]';

    /**
     * Selector for option's title.
     *
     * @var string
     */
    protected $optionTitle = './dt[%d]/label';

    /**
     * Selector for required option.
     *
     * @var string
     */
    protected $required = '//dt[label[text() = "%s"]]//self::*[contains(@class,"required")]';

    /**
     * Get configurable product options.
     *
     * @param InjectableFixture|null $product [optional]
     * @return array
     * @throws \Exception
     */
    public function getOptions(InjectableFixture $product)
    {
        /** @var ConfigurableProduct $product */
        $attributesData = $product->hasData('configurable_options')
            ? $product->getConfigurableOptions()['attributes_data']
            : [];
        $listOptions = $this->getListOptions();
        $result = [];

        foreach ($attributesData as $option) {
            $title = $option['label'];
            if (!isset($listOptions[$title])) {
                throw new \Exception("Can't find option: \"{$title}\"");
            }

            /** @var Element $optionElement */
            $result[$title] = $this->getOptionsData($listOptions, $option, $title);

            if (!empty($result) && $this->_rootElement->find('#attribute' . $option['attribute_id'])->isDisabled()) {
                $availableOptionData = current($result);
                foreach($availableOptionData['options'] as $optionKey => $value) {
                    $availableOptionData['type'] = 'dropdown';
                    $availableOptionData['value'] = $availableOptionData['options'][$optionKey]['title'];
                    $fillOption[0] = $availableOptionData;
                    unset($fillOption[0]['options'], $fillOption[0]['is_require']);
                    $this->fillOptions($fillOption);
                    $disableOptionData[$optionKey] = $this->getOptionsData($listOptions, $option, $title);
                    $result[$title]['options'][$optionKey] = $disableOptionData[$optionKey]['options'][0];
                }
            }
        }

        return $result;
    }

    /**
     * Get options data.
     *
     * @param $listOptions
     * @param $option
     * @param $title
     * @return Element
     */
    protected function getOptionsData($listOptions, $option, $title)
    {
        $optionElement = $listOptions[$title];
        $type = $option['frontend_input'];
        $option['frontend_input'] = explode('/', $option['frontend_input'])[1];
        $typeMethod = preg_replace('/[^a-zA-Z]/', '', $option['frontend_input']);
        $getTypeData = 'get' . ucfirst(strtolower($typeMethod)) . 'Data';

        $optionData = $this->$getTypeData($optionElement);
        $optionData['title'] = $title;
        $optionData['type'] = $type;
        $isRequire = $this->_rootElement->find(sprintf($this->required, $title), Locator::SELECTOR_XPATH)
            ->isVisible();
        $optionData['is_require'] = $isRequire ? 'Yes' : 'No';

        return $optionData;
    }

    /**
     * Get options context.
     *
     * @return Element
     */
    protected function getOptionsContext()
    {
        return $this->_rootElement->find(sprintf($this->optionsContext, 1), Locator::SELECTOR_XPATH);
    }
}
