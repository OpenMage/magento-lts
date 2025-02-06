<?php

/**
 * @category   Varien
 * @package    Varien_Event
 */

/**
 * Event regex observer object
 *
 * @category   Varien
 * @package    Varien_Event
 *
 * @method string getEventRegex()
 */
class Varien_Event_Observer_Regex extends Varien_Event_Observer
{
    /**
     * Checks the observer's event_regex against event's name
     *
     * @return boolean
     */
    public function isValidFor(Varien_Event $event)
    {
        return preg_match($this->getEventRegex(), $event->getName());
    }
}
