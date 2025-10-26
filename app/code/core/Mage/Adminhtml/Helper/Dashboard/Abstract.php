<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml abstract  dashboard helper.
 *
 * @package    Mage_Adminhtml
 */
abstract class Mage_Adminhtml_Helper_Dashboard_Abstract extends Mage_Core_Helper_Data
{
    /**
     * Helper collection
     *
     * @var Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Eav_Model_Entity_Collection_Abstract|array|null
     */
    protected $_collection;

    /**
     * Parameters for helper
     *
     * @var array
     */
    protected $_params = [];

    public function getCollection()
    {
        if (is_null($this->_collection)) {
            $this->_initCollection();
        }

        return $this->_collection;
    }

    abstract protected function _initCollection();

    /**
     * Returns collection items
     *
     * @return array
     */
    public function getItems()
    {
        return is_array($this->getCollection()) ? $this->getCollection() : $this->getCollection()->getItems();
    }

    public function getCount()
    {
        return count($this->getItems());
    }

    public function getColumn($index)
    {
        $result = [];
        foreach ($this->getItems() as $item) {
            if (is_array($item)) {
                if (isset($item[$index])) {
                    $result[] = $item[$index];
                } else {
                    $result[] = null;
                }
            } elseif ($item instanceof Varien_Object) {
                $result[] = $item->getData($index);
            } else {
                $result[] = null;
            }
        }

        return $result;
    }

    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
    }

    public function setParams(array $params)
    {
        $this->_params = $params;
    }

    public function getParam($name)
    {
        return $this->_params[$name] ?? null;
    }

    public function getParams()
    {
        return $this->_params;
    }
}
