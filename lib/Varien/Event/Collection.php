<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Event
 */

/**
 * Collection of events
 *
 * @package    Varien_Event
 */
class Varien_Event_Collection
{
    /**
     * Array of events in the collection
     *
     * @var array
     */
    protected $_events;

    /**
     * Global observers
     *
     * For example regex observers will watch all events that
     *
     * @var Varien_Event_Observer_Collection
     */
    protected $_observers;

    /**
     * @var Varien_Event_Observer_Collection
     */
    protected $_globalObservers;

    /**
     * Initializes global observers collection
     *
     */
    public function __construct()
    {
        $this->_events = [];
        $this->_globalObservers = new Varien_Event_Observer_Collection();
    }

    /**
     * Returns all registered events in collection
     *
     * @return array
     */
    public function getAllEvents()
    {
        return $this->_events;
    }

    /**
     * Returns all registered global observers for the collection of events
     *
     * @return Varien_Event_Observer_Collection
     */
    public function getGlobalObservers()
    {
        return $this->_globalObservers;
    }

    /**
     * Returns event by its name
     *
     * If event doesn't exist creates new one and returns it
     *
     * @param string $eventName
     * @return Varien_Event
     */
    public function getEventByName($eventName)
    {
        if (!isset($this->_events[$eventName])) {
            $this->addEvent(new Varien_Event(['name' => $eventName]));
        }

        return $this->_events[$eventName];
    }

    /**
     * Register an event for this collection
     *
     * @return Varien_Event_Collection
     */
    public function addEvent(Varien_Event $event)
    {
        $this->_events[$event->getName()] = $event;
        return $this;
    }

    /**
     * Register an observer
     *
     * If observer has event_name property it will be registered for this specific event.
     * If not it will be registered as global observer
     *
     * @return Varien_Event_Collection
     */
    public function addObserver(Varien_Event_Observer $observer)
    {
        $eventName = $observer->getEventName();
        if ($eventName) {
            $this->getEventByName($eventName)->addObserver($observer);
        } else {
            $this->getGlobalObservers()->addObserver($observer);
        }

        return $this;
    }

    /**
     * Dispatch event name with optional data
     *
     * Will dispatch specific event and will try all global observers
     *
     * @param string $eventName
     * @return Varien_Event_Collection
     */
    public function dispatch($eventName, array $data = [])
    {
        $event = $this->getEventByName($eventName);
        $event->addData($data)->dispatch();
        $this->getGlobalObservers()->dispatch($event);
        return $this;
    }
}
