<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Centinel
 */

/**
 * Abstract Validation State Model
 *
 * @package    Mage_Centinel
 */
abstract class Mage_Centinel_Model_StateAbstract extends Varien_Object
{
    /**
     * Storage data model
     *
     * @var Varien_Object|false
     */
    private $_dataStorage = false;

    /**
     * Setter for storage data model
     *
     * @param Varien_Object $dataStorageModel
     * @return Mage_Centinel_Model_StateAbstract
     */
    public function setDataStorage($dataStorageModel)
    {
        $this->_dataStorage = $dataStorageModel;
        return $this;
    }

    /**
     * Getter for storage data model
     *
     * @return Varien_Object
     */
    public function getDataStorage()
    {
        return $this->_dataStorage;
    }

    /**
     * Retrieves data from the object
     *
     * If $key is empty will return all the data as an array
     * Otherwise it will return value of the attribute specified by $key
     *
     * $index parameter is ignored
     * @see Mage_Core_Model_Session_Abstract::getData()
     *
     * @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        return $this->getDataStorage()->getData($key);
    }

    /**
     * Overwrite data in the object.
     *
     * $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed $value
     * @return Mage_Centinel_Model_StateAbstract
     */
    public function setData($key, $value = null)
    {
        $this->getDataStorage()->setData($key, $value);
        return $this;
    }

    /**
     * Save lookup result in state model
     *
     * @param Varien_Object $result
     * @return Mage_Centinel_Model_StateAbstract
     */
    public function setLookupResult($result)
    {
        foreach ($result->getData() as $key => $value) {
            $this->setData('lookup_' . $key, $value);
        }

        return $this;
    }

    /**
     * Save authenticate result in state model
     *
     * @param Varien_Object $result
     * @return Mage_Centinel_Model_StateAbstract
     */
    public function setAuthenticateResult($result)
    {
        foreach ($result->getData() as $key => $value) {
            $this->setData('authenticate_' . $key, $value);
        }

        return $this;
    }

    /**
     * Analyse lookup`s results. If lookup is successful return true and false if it failure
     * Result depends from flag self::getIsModeStrict()
     *
     * @return bool
     */
    final public function isLookupSuccessful()
    {
        if ($this->_isLookupStrictSuccessful()) {
            return true;
        } elseif (!$this->getIsModeStrict() && $this->_isLookupSoftSuccessful()) {
            return true;
        }

        return false;
    }

    /**
     * Analyse lookup`s results. If lookup is strict successful return true
     *
     * @return bool
     */
    abstract protected function _isLookupStrictSuccessful();

    /**
     * Analyse lookup`s results. If lookup is soft successful return true
     *
     * @return bool
     */
    abstract protected function _isLookupSoftSuccessful();

    /**
     * Analyse lookup`s results. If it has require params for authenticate, return true
     *
     * @return bool
     */
    abstract public function isAuthenticateAllowed();

    /**
     * Analyse authenticate`s results. If authenticate is successful return true and false if it failure
     * Result depends from flag self::getIsModeStrict()
     *
     * @return bool
     */
    abstract public function isAuthenticateSuccessful();
}
