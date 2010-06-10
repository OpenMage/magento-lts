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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml catalog product action attribute update helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute extends Mage_Core_Helper_Data
{
    /**
     * Selected products for massupdate
     *
     * @var Mage_Catalog_Model_Entity_Product_Collection
     */
    protected $_products;

    /**
     * Array of products that not available in selected store
     *
     * @var array
     */
    protected $_productsNotInStore;

    /**
     * Same attribtes for selected products
     *
     * @var Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected $_attributes;


    /**
     * Excluded from batch update attribute codes
     *
     * @var array
     */
    protected $_excludedAttributes = array('url_key');

    /**
     * Retrive product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getProducts()
    {
        if (is_null($this->_products)) {
            $productsIds = $this->getProductIds();

            if(!is_array($productsIds)) {
                $productsIds = array(0);
            }

            $this->_products = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($this->getSelectedStoreId())
                ->addIdFilter($productsIds);
                //->load();
                //->addStoreNamesToResult();
        }

        return $this->_products;
    }

    /**
     * Retrive selected products ids from post or session
     *
     * @return array|null
     */
    public function getProductIds()
    {
        $session = Mage::getSingleton('adminhtml/session');

        if ($this->_getRequest()->isPost() && $this->_getRequest()->getActionName()=='edit') {
            $session->setProductIds($this->_getRequest()->getParam('product', null));
        }

        return $session->getProductIds();
    }

    /**
     * Retrive selected store id
     *
     * @return integer
     */
    public function getSelectedStoreId()
    {
        return (int) $this->_getRequest()->getParam('store', 0);
    }

    /**
     * Retrive selected products' attribute sets
     *
     * @return array
     */
    public function getProductsSetIds()
    {
        return $this->getProducts()->getSetIds();
    }

    /**
     * Retrive same attributes for selected products without unique
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = $this->getProducts()->getEntity()->getEntityType()->getAttributeCollection()
                ->addIsNotUniqueFilter()
                ->setInAllAttributeSetsFilter($this->getProductsSetIds());

            foreach ($this->_excludedAttributes as $attributeCode) {
                $this->_attributes->addFieldToFilter('attribute_code', array('neq'=>$attributeCode));
            }

            $this->_attributes->load();
            foreach($this->_attributes as $attribute) {
                $attribute->setEntity($this->getProducts()->getEntity());
            }
        }

        return $this->_attributes;
    }

    /**
     * Retrive products ids that not available for selected store
     *
     * @return array
     */
    public function getProductsNotInStoreIds()
    {
        if (is_null($this->_productsNotInStore)) {
            $this->_productsNotInStoreIds = array();
            /*foreach ($this->getProducts() as $product) {
                $stores = $product->getStores();
                if (!isset($stores[$this->getSelectedStoreId()]) && $this->getSelectedStoreId() != 0) {
                    $this->_productsNotInStoreIds[] = $product->getId();
                }
            }*/
        }

        return $this->_productsNotInStoreIds;
    }

}
