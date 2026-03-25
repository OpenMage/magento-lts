<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert container abstract
 *
 * @package    Varien_Convert
 */
abstract class Varien_Convert_Container_Abstract implements Varien_Convert_Container_Interface
{
    protected $_vars;

    protected $_profile;

    protected $_data;

    protected $_position;

    public function getVar($key, $default = null)
    {
        if (!isset($this->_vars[$key])) {
            return $default;
        }

        return $this->_vars[$key];
    }

    public function getVars()
    {
        return $this->_vars;
    }

    public function setVar($key, $value = null)
    {
        if (is_array($key) && is_null($value)) {
            $this->_vars = $key;
        } else {
            $this->_vars[$key] = $value;
        }

        return $this;
    }

    public function getProfile()
    {
        return $this->_profile;
    }

    public function setProfile(Varien_Convert_Profile_Abstract $profile)
    {
        $this->_profile = $profile;
        return $this;
    }

    public function getData()
    {
        if (is_null($this->_data) && $this->getProfile()) {
            $this->_data = $this->getProfile()->getContainer()->getData();
        }

        return $this->_data;
    }

    public function setData($data)
    {
        if ($this->getProfile()) {
            $this->getProfile()->getContainer()->setData($data);
        }

        $this->_data = $data;
        return $this;
    }

    public function validateDataString($data = null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }

        if (!is_string($data)) {
            $this->addException('Invalid data type, expecting string.', Varien_Convert_Exception::FATAL);
        }

        return true;
    }

    public function validateDataArray($data = null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }

        if (!is_array($data)) {
            $this->addException('Invalid data type, expecting array.', Varien_Convert_Exception::FATAL);
        }

        return true;
    }

    public function validateDataGrid($data = null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }

        if (!is_array($data) || !is_array(current($data))) {
            if (count($data) == 0) {
                return true;
            }

            $this->addException('Invalid data type, expecting 2D grid array.', Varien_Convert_Exception::FATAL);
        }

        return true;
    }

    public function getGridFields($grid)
    {
        $fields = [];
        foreach ($grid as $row) {
            foreach ($row as $fieldName => $data) {
                if (!in_array($fieldName, $fields)) {
                    $fields[] = $fieldName;
                }
            }
        }

        return $fields;
    }

    public function addException($error, $level = null)
    {
        $e = new Varien_Convert_Exception($error);
        $e->setLevel(is_null($level) ? Varien_Convert_Exception::NOTICE : $level);
        $e->setContainer($this);
        $e->setPosition($this->getPosition());

        if ($this->getProfile()) {
            $this->getProfile()->addException($e);
        }

        return $e;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }
}
