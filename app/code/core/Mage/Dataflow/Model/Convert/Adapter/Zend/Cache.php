<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Convert zend cache adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Adapter_Zend_Cache extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Zend_Cache::factory($this->getVar('frontend', 'Core'), $this->getVar('backend', 'File'));
        }
        if ($this->_resource->getBackend() instanceof Zend_Cache_Backend_Static) {
            throw new Exception(Mage::helper('dataflow')->__('Backend name "Static" not supported.'));
        }
        return $this->_resource;
    }

    public function load()
    {
        $this->setData($this->getResource()->load($this->getVar('id')));
        return $this;
    }

    public function save()
    {
        $this->getResource()->save($this->getData(), $this->getVar('id'));
        return $this;
    }
}
