<?php
/**
 * Convert zend db adapter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Varien_Convert
 */
/**
 * @package    Varien_Convert
 */


class Varien_Convert_Adapter_Zend_Db extends Varien_Convert_Adapter_Abstract
{
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Zend_Db::factory($this->getVar('adapter', 'Pdo_Mysql'), $this->getVars());
        }
        return $this->_resource;
    }

    public function load()
    {
        return $this;
    }

    public function save()
    {
        return $this;
    }
}
