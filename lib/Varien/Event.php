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
 * @category    Varien
 * @package     Varien_Event
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Event object and dispatcher
 *
 * @category   Varien
 * @package    Varien_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Event extends Varien_Object
{
    /**
     * Observers collection
     *
     * @var Varien_Event_Observer_Collection
     */
    protected $_observers;

    /**
     * Constructor
     *
     * Initializes observers collection
     *
     * @param array $data
     */
    public function __construct(array $data=array())
    {
        $this->_observers = new Varien_Event_Observer_Collection();
        parent::__construct($data);
    }

    /**
     * Returns all the registered observers for the event
     *
     * @return Varien_Event_Observer_Collection
     */
    public function getObservers()
    {
        return $this->_observers;
    }

    /**
     * Register an observer for the event
     *
     * @param Varien_Event_Observer $observer
     * @return Varien_Event
     */
    public function addObserver(Varien_Event_Observer $observer)
    {
        $this->getObservers()->addObserver($observer);
        return $this;
    }

    /**
     * Removes an observer by its name
     *
     * @param string $observerName
     * @return Varien_Event
     */
    public function removeObserverByName($observerName)
    {
        $this->getObservers()->removeObserverByName($observerName);
        return $this;
    }

    /**
     * Dispatches the event to registered observers
     *
     * @return Varien_Event
     */
    public function dispatch()
    {
        $this->getObservers()->dispatch($this);
        return $this;
    }

    /**
     * Retrieve event name
     *
     * @return string
     */
    public function getName()
    {
        return isset($this->_data['name']) ? $this->_data['name'] : null;
    }

    public function setName($data)
    {
        $this->_data['name'] = $data;
        return $this;
    }

    public function getBlock()
    {
        return $this->_getData('block');
    }
}
