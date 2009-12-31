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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product links collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_product;
    protected $_linkModel;
    protected $_linkTypeId;

    protected function _construct()
    {
        $this->_init('catalog/product_link');
    }

    /**
     * Declare link model and initialize type attributes join
     *
     * @param   Mage_Catalog_Model_Product_Link $linkModel
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Collection
     */
    public function setLinkModel($linkModel)
    {
        $this->_linkModel = $linkModel;
        if ($linkModel->getLinkTypeId()) {
            $this->_linkTypeId = $linkModel->getLinkTypeId();
        }
        return $this;
    }
    
    /**
     * Retrieve collection link model
     *
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function getLinkModel()
    {
        return $this->_linkModel;
    }
    
    /**
     * Initialize collection parent product and add limitation join
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Collection
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Retrieve collection base product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }
    
    /**
     * Add link's type to filter
     *
     * @return Mage_Catalog_Model_Product
     */
    public function addLinkTypeIdFilter()
    {
        if ($this->_linkTypeId) {
            $this->addFieldToFilter("link_type_id", $this->_linkTypeId);
        }
        return $this;
    }

    /**
     * Add product to filter
     *
     * @return Mage_Catalog_Model_Product
     */
    public function addProductIdFilter()
    {
        if ($this->getProduct() && $this->getProduct()->getId()) {
            $this->addFieldToFilter("product_id", $this->getProduct()->getId());
        }
        return $this;
    }
    
    /**
     * Join attributes
     *
     * @return Mage_Catalog_Model_Product
     */
    public function joinAttributes()
    {
        if ($this->getLinkModel()) {
            $attributes = $this->getLinkModel()->getAttributes();
            $attributesByType = array();
            foreach ($attributes as $attribute) {
                $table = $this->getLinkModel()->getAttributeTypeTable($attribute['type']);
                $alias = 'link_attribute_'.$attribute['code'].'_'.$attribute['type'];
                $this->getSelect()->joinLeft(
                    array($alias => $table),
                    $alias.'.link_id=main_table.link_id AND '.$alias.'.product_link_attribute_id='.$attribute['id'],
                    array($attribute['code'] => 'value')
                );
            }
        }
        return $this;
    }
}