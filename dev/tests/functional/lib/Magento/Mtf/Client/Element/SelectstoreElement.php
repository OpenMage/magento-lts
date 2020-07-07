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

namespace Magento\Mtf\Client\Element;

use Magento\Mtf\Client\Locator;

/**
 * Typified element class for option group selectors.
 */
class SelectstoreElement extends SelectElement
{
    /**
     * Store option group selector.
     *
     * @var string
     */
    protected $storeGroup = 'optgroup[option[contains(.,"%s")]]';

    /**
     * Website option group selector.
     *
     * @var string
     */
    protected $website = 'optgroup[following-sibling::optgroup[option[contains(.,"%s")]]]';

    /**
     * Get the value of form element.
     *
     * @return string
     */
    public function getValue()
    {
        $selectedLabel = trim(parent::getValue());
        $element = $this->find(sprintf($this->website, $selectedLabel), Locator::SELECTOR_XPATH);
        $value = trim($element->getAttribute('label'));
        $element = $this->find(sprintf($this->storeGroup, $selectedLabel), Locator::SELECTOR_XPATH);
        $value .= '/' . trim($element->getAttribute('label'), chr(0xC2) . chr(0xA0));
        $value .= '/' . $selectedLabel;
        return $value;
    }

    /**
     * Select value in dropdown which has option groups.
     *
     * @param string $value
     * @throws \Exception
     * @return void
     */
    public function setValue($value)
    {
        $pieces = explode('/', $value);

        if (1 == count($pieces)) {
            $optionLocator = './/option[contains(text(),"' . $pieces[0] . '")]';
        } else {
            $optionLocator = './/optgroup[contains(@label,"'
                . $pieces[0] . '")]/following-sibling::optgroup[contains(@label,"'
                . $pieces[1] . '")]/option[contains(text(), "'
                . $pieces[2] . '")]';
        }

        $option = $this->context->find($optionLocator, Locator::SELECTOR_XPATH);
        if (!$option->isVisible()) {
            throw new \Exception('[' . implode('/', $pieces) . '] option is not visible in store switcher.');
        }
        $option->click();
    }
}
