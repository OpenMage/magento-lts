<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Event
 */

/**
 * Event observer object
 *
 * @package    Varien_Event
 */
class Varien_Event_Observer extends Varien_Object
{
    /**
     * Checks the observer's event_regex against event's name
     *
     * @return bool
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

        $profilerKey = 'OBSERVER: ' . (is_object($callback[0]) ? $callback[0]::class : (string) $callback[0]) . ' -> ' . $callback[1];
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
