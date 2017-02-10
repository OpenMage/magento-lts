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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Attribute;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement;
use Magento\Mtf\Client\DriverInterface;
use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Catalog product custom attribute element.
 */
class CustomAttribute extends SimpleElement
{
    /**
     * Attribute input selector.
     *
     * @var string
     */
    protected $inputSelector = '#%s';

    /**
     * Attribute label.
     *
     * @var string
     */
    protected $attributeLabel = './/td[1]/label';

    /**
     * Attribute class to element type reference.
     *
     * @var array
     */
    protected $classReference = [
        'input-text' => null,
        'textarea' => null,
        'hasDatepicker' => 'datepicker',
        'select' => 'select',
        'multiselect' => 'multiselect',
    ];

    /**
     * @constructor
     * @param DriverInterface $driver
     * @param EventManagerInterface $eventManager
     * @param Locator $locator
     * @param ElementInterface $context
     */
    public function __construct(
        DriverInterface $driver,
        EventManagerInterface $eventManager,
        Locator $locator,
        ElementInterface $context = null
    ) {
        parent:: __construct($driver, $eventManager, $locator, $context);

        $this->attributeLabel = $this->getAttributeLabelFromPage();
    }

    /**
     * Get attribute label.
     *
     * @return mixed
     */
    protected function getAttributeLabelFromPage()
    {
        $label = $this->find($this->attributeLabel, Locator::SELECTOR_XPATH)->getText();
        return str_replace(' *', '', $label);
    }

    /**
     * Set attribute value.
     *
     * @param array|string $data
     * @return void
     */
    public function setValue($data)
    {
        $this->eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $element = $this->getElementByClass($this->getElementClass());
        $value = is_array($data) ? $data['value'] : $data;
        $this->find(sprintf($this->inputSelector, $this->attributeLabel), Locator::SELECTOR_CSS, $element)
            ->setValue($value);
    }

    /**
     * Get custom attribute value.
     *
     * @return string|array
     */
    public function getValue()
    {
        $this->eventManager->dispatchEvent(['get_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $inputType = $this->getElementByClass($this->getElementClass());
        return $this->find(sprintf($this->inputSelector, $this->attributeLabel), Locator::SELECTOR_CSS, $inputType)
            ->getValue();
    }

    /**
     * Get custom attribute values.
     *
     * @return array
     */
    public function getValues()
    {
        $result = [];
        $this->eventManager->dispatchEvent(['get_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $inputType = $this->getElementByClass($this->getElementClass());
        $element = $this->find(sprintf($this->inputSelector, $this->attributeLabel), Locator::SELECTOR_CSS, $inputType);
        $values = $element->getElements('option');
        foreach ($values as $value) {
            $result[] = $value->getText();
        }

        return array_reverse($this->prepareResult($inputType, $result));
    }

    /**
     * Prepare result.
     *
     * @param string $inputType
     * @param array $result
     * @return array
     */
    protected function prepareResult($inputType, array $result)
    {
        if ($inputType == 'multiselect') {
            return $result;
        } else {
            array_shift($result);
            return $result;
        }
    }

    /**
     * Get element type by class.
     *
     * @param string $class
     * @return string
     */
    protected function getElementByClass($class)
    {
        $element = null;
        foreach ($this->classReference as $key => $reference) {
            if (strpos($class, $key) !== false) {
                $element = $reference;
            }
        }
        return $element;
    }

    /**
     * Get element class.
     *
     * @return string
     */
    protected function getElementClass()
    {
        return $this->find(sprintf($this->inputSelector, $this->attributeLabel))->getAttribute('class');
    }
}
