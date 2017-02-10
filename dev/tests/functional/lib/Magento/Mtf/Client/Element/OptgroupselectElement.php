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

namespace Magento\Mtf\Client\Element;

use Magento\Mtf\Client\Locator;

/**
 * Typified element class for option group selectors.
 */
class OptgroupselectElement extends SelectElement
{
    /**
     * Option locator.
     *
     * @var string
     */
    protected $optionByIndex = './/option';

    /**
     * Option group selector.
     *
     * @var string
     */
    protected $optGroup = 'optgroup[option[contains(.,"%s")]]';

    /**
     * Option group locator.
     *
     * @var string
     */
    protected $optionGroupValue = ".//optgroup[contains(@label, '%s')]/option[contains(text(),'%s')]";

    /**
     * Option group locator when exact value should be used
     *
     * @var string
     */
    protected $optionGroupExactValue = ".//optgroup[contains(@label, '%s')]/option[text() = '%s']";

    /**
     * Get the value of form element.
     *
     * @throws \Exception
     * @return string
     */
    public function getValue()
    {
        $this->eventManager->dispatchEvent(['get_value'], [(string)$this->getAbsoluteSelector()]);

        $selectedLabel = '';
        $labels = $this->getElements($this->optionByIndex, Locator::SELECTOR_XPATH);
        foreach ($labels as $label) {
            if ($label->isSelected()) {
                $selectedLabel = $label->getText();
                break;
            }
        }
        if ($selectedLabel == '') {
            throw new \Exception('Selected value has not been found in optgroup select.');
        }

        $element = $this->find(sprintf($this->optGroup, $selectedLabel), Locator::SELECTOR_XPATH);
        $value = trim($element->getAttribute('label'), chr(0xC2) . chr(0xA0));
        $value .= '/' . $selectedLabel;

        return $value;
    }

    /**
     * Select value in dropdown which has option groups.
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        list($group, $option) = explode('/', $value);
        $xpath = (($option != 'Time')
            ? (sprintf($this->optionGroupValue, $group, $option))
            : (sprintf($this->optionGroupExactValue, $group, $option)));
        $option = $this->find($xpath, Locator::SELECTOR_XPATH);
        $option->click();
    }
}
