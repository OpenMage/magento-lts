<?php
/**
 * Event regex observer object
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Varien_Event
 * @method string getEventRegex()
 */
/**
 * @package    Varien_Event
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
