<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index data retriever factory
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @var Mage_Customer_Model_Resource_Group_Collection
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
     * @return Mage_CatalogIndex_Model_Data_Abstract
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
