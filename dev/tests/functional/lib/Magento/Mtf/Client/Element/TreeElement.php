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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Client\Element;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\ElementInterface;

/**
 * Typified element class for Tree elements.
 */
class TreeElement extends Element
{
    /**
     * All selected checkboxes.
     *
     * @var string
     */
    protected $selectedCheckboxes = '//input[@checked=""]';

    /**
     * Selected checkboxes.
     *
     * @var string
     */
    protected $selectedLabels = '//input[@checked=""]/../a/span';

    /**
     * Pattern for child category node.
     *
     * @var string
     */
    protected $pattern = '//li[@class="x-tree-node" and div/a/span[contains(text(),"%s")]]';

    /**
     * Selector for plus image.
     *
     * @var string
     */
    protected $imagePlus = './div/img[contains(@class, "-plus")]';

    /**
     * Selector for child loader.
     *
     * @var string
     */
    protected $childLoader = 'ul';

    /**
     * Selector for input.
     *
     * @var string
     */
    protected $input = '/div/a/span';

    /**
     * Selector for parent element.
     *
     * @var string
     */
    protected $parentElement = './../../../../../div/a/span';

    /**
     * Clear data for element.
     *
     * @return void
     */
    public function clear()
    {
        $checkboxes = $this->getElements($this->selectedCheckboxes, Locator::SELECTOR_XPATH, 'checkbox');
        foreach ($checkboxes as $checkbox) {
            $checkbox->setValue('No');
        }
    }

    /**
     * Get the value.
     *
     * @return array
     */
    public function getValue()
    {
        $this->eventManager->dispatchEvent(['get_value'], [(string)$this->getAbsoluteSelector()]);
        $checkboxes = $this->getElements($this->selectedLabels, Locator::SELECTOR_XPATH);
        $values = [];
        foreach ($checkboxes as $checkbox) {
            $fullPath = $this->getFullPath($checkbox);
            $values[] = implode('/', array_reverse($fullPath));
        }

        return $values;
    }

    /**
     * Get full path for element.
     *
     * @param ElementInterface $element
     * @return string[]
     */
    protected function getFullPath(ElementInterface $element)
    {
        $fullPath[] = $this->getElementLabel($element);
        $parentElement = $element->find($this->parentElement, Locator::SELECTOR_XPATH);
        if ($parentElement->isVisible()) {
            $fullPath = array_merge($fullPath, $this->getFullPath($parentElement));
        }

        return $fullPath;
    }

    /**
     * Get element label.
     *
     * @param ElementInterface $element
     * @return string
     */
    protected function getElementLabel(ElementInterface $element)
    {
        $value = $element->getText();
        preg_match('`(.+) \(.*`', $value, $matches);

        return $matches[1];
    }

    /**
     * Click a tree element by its path (Node names) in tree.
     *
     * @param string $path
     * @return void
     */
    public function setValue($path)
    {
        $this->eventManager->dispatchEvent(['set_value'], [(string)$this->getAbsoluteSelector()]);
        $this->clear();
        $elementSelector = $this->prepareElementSelector($path);
        $elements = $this->getElements($elementSelector . $this->input, Locator::SELECTOR_XPATH);
        foreach ($elements as $element) {
            $element->click();
        }
    }

    /**
     * Prepare element selector.
     *
     * @param string $path
     * @return string
     */
    protected function prepareElementSelector($path)
    {
        $pathArray = explode('/', $path);
        $elementSelector = '';
        foreach ($pathArray as $itemElement) {
            $this->displayChildren($itemElement);
            $elementSelector .= sprintf($this->pattern, $itemElement);
        }

        return $elementSelector;
    }

    /**
     * Check visible element.
     *
     * @param string $path
     * @return bool
     */
    public function isElementVisible($path)
    {
        $elementSelector = $this->prepareElementSelector($path);
        return $this->find($elementSelector, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Display children.
     *
     * @param $element
     * @return void
     */
    protected function displayChildren($element)
    {
        $element = $this->find(sprintf($this->pattern, $element), Locator::SELECTOR_XPATH);
        $plusButton = $element->find($this->imagePlus, Locator::SELECTOR_XPATH);
        if ($plusButton->isVisible()) {
            $plusButton->click();
            $this->waitLoadChildren($element);
        }
    }

    /**
     * Waiter for load children.
     *
     * @param ElementInterface $element
     * @return void
     */
    protected function waitLoadChildren(ElementInterface $element)
    {
        $selector = $this->childLoader;
        $this->waitUntil(
            function () use ($element, $selector) {
                return $element->find($selector)->isVisible() ? true : null;
            }
        );
    }

    /**
     * keys method is not accessible in this class.
     * Throws exception if used.
     *
     * @param array $keys
     * @throws \BadMethodCallException
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function keys(array $keys)
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (TreeElement)');
    }

    /**
     * Drag'n'drop method is not accessible in this class.
     * Throws exception if used.
     *
     * @param ElementInterface $target
     * @throws \BadMethodCallException
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function dragAndDrop(ElementInterface $target)
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (TreeElement)');
    }

}
