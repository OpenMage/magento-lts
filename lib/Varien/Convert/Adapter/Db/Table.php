<?php

/**
 * @category   Varien
 * @package    Varien_Convert
 */

/**
 * Convert db table adapter
 *
 * @category   Varien
 * @package    Varien_Convert
 */
class Varien_Convert_Adapter_Db_Table extends Varien_Convert_Adapter_Abstract
{
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Zend_Db::factory($this->getVar('type'), $this->getVars());
        }
        return $this->_resource;
    }

    public function load() {}

    public function save() {}
}
