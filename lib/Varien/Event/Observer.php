<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Event
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Event observer object
 *
 * @category   Varien
 * @package    Varien_Event
 */
class Varien_Event_Observer extends Varien_Object
{
    /**
     * Checks the observer's event_regex against event's name
     *
     * @return boolean
     */
    public function isValidFor(Varien_Event $event)
    {
        return $this->getEventName() === $event->getName();
    }

    /**
     * Dispatches an event to observer's callback
     *
     * @return $this
     */
    public function dispatch(Varien_Event $event)
    {
        if (!$this->isValidFor($event)) {
            return $this;
        }

        $callback = $this->getCallback();
        $this->setEvent($event);

        $profilerKey = 'OBSERVER: ' . (is_object($callback[0]) ? get_class($callback[0]) : (string) $callback[0]) . ' -> ' . $callback[1];
        Varien_Profiler::start($profilerKey);
        call_user_func($callback, $this);
        Varien_Profiler::stop($profilerKey);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setName($data)
    {
        return $this->setData('name', $data);
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->getData('event_name');
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setEventName($data)
    {
        return $this->setData('event_name', $data);
    }

    /**
     * @return array
     */
    public function getCallback()
    {
        return $this->getData('callback');
    }

    /**
     * @param $data
     * @return $this
     */
    public function setCallback($data)
    {
        return $this->setData('callback', $data);
    }

    /**
     * Get observer event object
     *
     * @return Varien_Event
     */
    public function getEvent()
    {
        return $this->getData('event');
    }

    /**
     * @param Varien_Event $data
     * @return $this
     */
    public function setEvent($data)
    {
        return $this->setData('event', $data);
    }
}
