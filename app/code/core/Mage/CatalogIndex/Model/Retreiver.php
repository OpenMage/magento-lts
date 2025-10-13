<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Index data retriever factory
 *
 * @package    Mage_CatalogIndex
 *
 * @method Mage_CatalogIndex_Model_Resource_Retreiver _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Retreiver getResource()
 * @method int getEntityTypeId()
 * @method $this setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method $this setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method $this setTypeId(string $value)
 * @method string getSku()
 * @method $this setSku(string $value)
 * @method int getHasOptions()
 * @method $this setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method $this setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 */
class Mage_CatalogIndex_Model_Retreiver extends Mage_Core_Model_Abstract
{
    public const CHILDREN_FOR_TIERS = 1;

    public const CHILDREN_FOR_PRICES = 2;

    public const CHILDREN_FOR_ATTRIBUTES = 3;

    protected $_attributeIdCache = [];

    /**
     * Customer group cache
     *
     * @var Mage_Customer_Model_Resource_Group_Collection|null
     */
    protected $_customerGroups;

    /**
     * Retriever model names cache
     *
     * @var array
     */
    protected $_retreivers = [];

    /**
     * Retriever factory init, load retriever settings
     *
     */
    protected function _construct()
    {
        $config = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
        foreach ($config as $type => $data) {
            if (isset($data['index_data_retreiver'])) {
                $this->_retreivers[$type] = $data['index_data_retreiver'];
            }
        }

        $this->_init('catalogindex/retreiver');
    }

    /**
     * Returns data retriever model by specified product type
     *
     * @param string $type
     * @return Mage_CatalogIndex_Model_Data_Abstract|false
     * @throws Mage_Core_Exception
     */
    public function getRetreiver($type)
    {
        if (isset($this->_retreivers[$type])) {
            return Mage::getSingleton($this->_retreivers[$type]);
        } else {
            Mage::throwException("Data retreiver for '{$type}' is not defined");
        }
    }

    /**
     * Return customer group collection
     *
     * @return Mage_Customer_Model_Resource_Group_Collection
     */
    public function getCustomerGroups()
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = Mage::getModel('customer/group')->getCollection();
        }

        return $this->_customerGroups;
    }

    /**
     * Return product ids sorted by type
     *
     * @param array $products
     * @return array
     */
    public function assignProductTypes($products)
    {
        $flat = $this->_getResource()->getProductTypes($products);
        $result = [];
        foreach ($flat as $one) {
            $result[$one['type']][] = $one['id'];
        }

        return $result;
    }
}
