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

/**
 * This class replaces vendor Magento\Mtf\Client\Element\SimpleElement because of problem described in MPERF-10217
 * The only difference between current class and original one is added new method isPresent
 *
 * @see Magento\Mtf\Client\Element\SimpleElement
 */
namespace Magento\Mtf\Client\Element;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\DriverInterface;
use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Class SimpleElement
 */
class SimpleElement implements ElementInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Wrapped element
     *
     * @var DriverInterface
     */
    protected $driver;

    /**
     * Element locator
     *
     * @var Locator
     */
    protected $locator;

    /**
     * Element context
     *
     * @var ElementInterface
     */
    protected $context;

    /**
     * Constructor
     *
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
        $this->driver = $driver;
        $this->eventManager = $eventManager;
        $this->locator = $locator;
        $this->context = $context;
    }

    /**
     * Drag and drop element to(between) another element(s)
     *
     * @param ElementInterface $target
     * @return void
     */
    public function dragAndDrop(ElementInterface $target)
    {
        $this->driver->dragAndDrop($this, $target);
    }

    /**
     * Check whether element is visible
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->driver->isVisible($this);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getText()
    {
        return $this->driver->getText($this);
    }

    /**
     * Find element by locator in context of current element
     *
     * @param string $selector
     * @param string $strategy [optional]
     * @param null|string $type [optional]
     * @return ElementInterface
     */
    public function find($selector, $strategy = Locator::SELECTOR_CSS, $type = null)
    {
        return $this->driver->find($selector, $strategy, $type, $this);
    }

    /**
     * Get all elements by locator
     *
     * @param string $selector
     * @param string $strategy
     * @param null|string $type
     * @return ElementInterface[]
     */
    public function getElements($selector, $strategy = Locator::SELECTOR_CSS, $type = null)
    {
        return $this->driver->getElements($this, $selector, $strategy, $type);
    }

    /**
     * Wait until callback isn't null or timeout occurs
     *
     * @param callable $callback
     * @return mixed
     */
    public function waitUntil($callback)
    {
        return $this->driver->waitUntil($callback);
    }

    /**
     * Click
     *
     * @return void
     */
    public function click()
    {
        $this->driver->click($this);
    }

    /**
     * Send keys
     *
     * @param array $keys
     * @return void
     */
    public function keys(array $keys)
    {
        $this->driver->keys($this, $keys);
    }

    /**
     * Check whether element is enabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->driver->isDisabled($this);
    }

    /**
     * Check whether element is present in the DOM.
     *
     * @return bool
     */
    public function isPresent()
    {
        return $this->driver->isPresent($this);
    }

    /**
     * Check whether element is selected
     *
     * @return bool
     */
    public function isSelected()
    {
        return $this->driver->isSelected($this);
    }

    /**
     * Set the value
     *
     * @param string|array $value
     * @return void
     */
    public function setValue($value)
    {
        $this->driver->setValue($this, $value);
    }

    /**
     * Get the value
     *
     * @return string|array
     */
    public function getValue()
    {
        return $this->driver->getValue($this);
    }

    /**
     * Get the value of a the given attribute of the element
     *
     * @param string $name
     * @return string
     */
    public function getAttribute($name)
    {
        return $this->driver->getAttribute($this, $name);
    }

    /**
     * Double click
     *
     * @return void
     */
    public function doubleClick()
    {
        $this->driver->doubleClick($this);
    }

    /**
     * Right click
     *
     * @return void
     */
    public function rightClick()
    {
        $this->driver->rightClick($this);
    }

    /**
     * Get absolute selector (for DBG)
     *
     * @return string
     */
    public function getAbsoluteSelector()
    {
        $selectors = [];
        $selectors[] = $this->getLocator();
        $element = $this;
        while($element = $element->getContext()) {
            $selectors[] = $element->getLocator();
        }

        return implode(' -> ', array_reverse($selectors));
    }

    /**
     * Get element locator
     *
     * @return Locator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Get context element
     *
     * @return ElementInterface|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Hover mouse over an element.
     *
     * @return void
     */
    public function hover()
    {
        $this->driver->hover($this);
    }
}
