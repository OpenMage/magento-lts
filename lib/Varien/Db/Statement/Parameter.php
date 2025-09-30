<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Db
 */

/**
 * Varien DB Statement Parameter
 *
 * Used to transmit specific information about parameter value binding to be bound the right
 * way to the query.
 * Most used properties and methods are defined in interface. Specific things for concrete DB adapter can be
 * transmitted using 'additional' property (Varien_Object) as a container.
 *
 * @package    Varien_Db
 */
class Varien_Db_Statement_Parameter
{
    /**
     * Actual parameter value
     */
    protected $_value = null;

    /**
     * Value is a BLOB.
     *
     * A shortcut setting to notify DB adapter, that value must be bound in a default way, as adapter binds
     * BLOB data to query placeholders. If FALSE, then specific settings from $_dataType, $_length,
     * $_driverOptions will be used.
     */
    protected $_isBlob = false;

    /**
     * Data type to set to DB driver during parameter bind
     */
    protected $_dataType = null;

    /**
     * Length to set to DB driver during parameter bind
     */
    protected $_length = null;

    /**
     * Specific driver options to set to DB driver during parameter bind
     */
    protected $_driverOptions = null;

    /**
     * Additional information to be used by DB adapter internally
     */
    protected $_additional = null;

    /**
     * Inits instance
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->_value = $value;
        $this->_additional = new Varien_Object();
    }

    /**
     * Sets parameter value.
     *
     * @param mixed $value
     * @return Varien_Db_Statement_Parameter
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Gets parameter value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets, whether parameter is a BLOB.
     *
     * FALSE (default) means, that concrete binding options come in dataType, length and driverOptions properties.
     * TRUE means that DB adapter must ignore other options and use adapter's default options to bind this parameter
     * as a BLOB value.
     *
     * @param bool $isBlob
     * @return Varien_Db_Statement_Parameter
     */
    public function setIsBlob($isBlob)
    {
        $this->_isBlob = $isBlob;
        return $this;
    }

    /**
     * Gets, whether parameter is a BLOB.
     * See setIsBlob() for returned value explanation.
     *
     * @return bool
     *
     * @see setIsBlob
     */
    public function getIsBlob()
    {
        return $this->_isBlob;
    }

    /**
     * Sets data type option to be used during binding parameter value.
     *
     * @param mixed $dataType
     * @return Varien_Db_Statement_Parameter
     */
    public function setDataType($dataType)
    {
        $this->_dataType = $dataType;
        return $this;
    }

    /**
     * Gets data type option to be used during binding parameter value.
     *
     * @return mixed
     */
    public function getDataType()
    {
        return $this->_dataType;
    }

    /**
     * Sets length option to be used during binding parameter value.
     *
     * @param mixed $length
     * @return Varien_Db_Statement_Parameter
     */
    public function setLength($length)
    {
        $this->_length = $length;
        return $this;
    }

    /**
     * Gets length option to be used during binding parameter value.
     *
     * @return mixed
     */
    public function getLength()
    {
        return $this->_length;
    }

    /**
     * Sets specific driver options to be used during binding parameter value.
     *
     * @param mixed $driverOptions
     * @return Varien_Db_Statement_Parameter
     */
    public function setDriverOptions($driverOptions)
    {
        $this->_driverOptions = $driverOptions;
        return $this;
    }

    /**
     * Gets driver options to be used during binding parameter value.
     *
     * @return mixed
     */
    public function getDriverOptions()
    {
        return $this->_driverOptions;
    }

    /**
     * Sets additional information for concrete DB adapter.
     * Set there any data you want to pass along with query parameter.
     *
     * @param Varien_Object $additional
     * @return Varien_Db_Statement_Parameter
     */
    public function setAdditional($additional)
    {
        $this->_additional = $additional;
        return $this;
    }

    /**
     * Gets additional information for concrete DB adapter.
     *
     * @return Varien_Object
     */
    public function getAdditional()
    {
        return $this->_additional;
    }

    /**
     * Returns representation of a object to be used in string contexts
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->_value;
    }

    /**
     * Returns representation of a object to be used in string contexts
     *
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }
}
