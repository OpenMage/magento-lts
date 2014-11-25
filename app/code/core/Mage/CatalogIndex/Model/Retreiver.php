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
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index data retreiver factory
 *
 * @method Mage_CatalogIndex_Model_Resource_Retreiver _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Retreiver getResource()
 * @method int getEntityTypeId()
 * @method Mage_CatalogIndex_Model_Retreiver setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method Mage_CatalogIndex_Model_Retreiver setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method Mage_CatalogIndex_Model_Retreiver setTypeId(string $value)
 * @method string getSku()
 * @method Mage_CatalogIndex_Model_Retreiver setSku(string $value)
 * @method int getHasOptions()
 * @method Mage_CatalogIndex_Model_Retreiver setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method Mage_CatalogIndex_Model_Retreiver setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method Mage_CatalogIndex_Model_Retreiver setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Mage_CatalogIndex_Model_Retreiver setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Retreiver extends Mage_Core_Model_Abstract
{
    const CHILDREN_FOR_TIERS = 1;
    const CHILDREN_FOR_PRICES = 2;
    const CHILDREN_FOR_ATTRIBUTES = 3;

    protected $_attributeIdCache = array();

    /**
     * Customer group cache
     *
     * @var Mage_Customer_Model_Mysql4_Group_Collection
     */
    protected $_customerGroups;

    /**
     * Retreiver model names cache
     *
     * @var array
     */
    protected $_retreivers = array();

    /**
     * Retreiver factory init, load retreiver settings
     *
     */
    protected function _construct()
    {
        $config = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
        foreach ($config as $type=>$data) {
            if (isset($data['index_data_retreiver'])) {
                $this->_retreivers[$type] = $data['index_data_retreiver'];
            }
        }

        $this->_init('catalogindex/retreiver');
    }

    /**
     * Returns data retreiver model by specified product type
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
     * @return Mage_Customer_Model_Entity_Group_Collection
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
        $result = array();
        foreach ($flat as $one) {
            $result[$one['type']][] = $one['id'];
        }
        return $result;
    }
}
