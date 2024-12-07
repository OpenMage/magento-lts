<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product action attribute update helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute extends Mage_Core_Helper_Data
{
    /**
     * Selected products for mass-update
     *
     * @var Mage_Catalog_Model_Resource_Product_Collection|null
     */
    protected $_products;

    /**
     * Array of same attributes for selected products
     *
     * @var Mage_Eav_Model_Resource_Entity_Attribute_Collection|null
     */
    protected $_attributes;

    /**
     * Excluded from batch update attribute codes
     *
     * @var array
     */
    protected $_excludedAttributes = ['url_key'];

    /**
     * Return product collection with selected product filter
     * Product collection didn't load
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProducts()
    {
        if (is_null($this->_products)) {
            $productsIds = $this->getProductIds();

            if (!is_array($productsIds)) {
                $productsIds = [0];
            }

            $this->_products = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($this->getSelectedStoreId())
                ->addIdFilter($productsIds);
        }

        return $this->_products;
    }

    /**
     * Return array of selected product ids from post or session
     *
     * @return array|string|null
     */
    public function getProductIds()
    {
        /** @var Mage_Adminhtml_Model_Session $session */
        $session = Mage::getSingleton('adminhtml/session');

        if ($this->_getRequest()->isPost() && strtolower($this->_getRequest()->getActionName()) === 'edit') {
            $session->setProductIds($this->_getRequest()->getParam('product', null));
        }

        return $session->getProductIds();
    }

    /**
     * Return selected store id from request
     *
     * @return int
     */
    public function getSelectedStoreId()
    {
        return (int)$this->_getRequest()->getParam('store', Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /**
     * Return array of attribute sets by selected products
     *
     * @return array
     */
    public function getProductsSetIds()
    {
        return $this->getProducts()->getSetIds();
    }

    /**
     * Return collection of same attributes for selected products without unique
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes  = Mage::getSingleton('eav/config')
                ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
                ->getAttributeCollection()
                ->addIsNotUniqueFilter()
                ->addFieldToFilter('frontend_input', ['neq' => 'label'])
                ->setInAllAttributeSetsFilter($this->getProductsSetIds());

            if ($this->_excludedAttributes) {
                $this->_attributes->addFieldToFilter('attribute_code', ['nin' => $this->_excludedAttributes]);
            }

            // check product type apply to limitation and remove attributes that impossible to change in mass-update
            $productTypeIds  = $this->getProducts()->getProductTypeIds();
            /** @var Mage_Catalog_Model_Entity_Attribute $attribute */
            foreach ($this->_attributes as $attribute) {
                foreach ($productTypeIds as $productTypeId) {
                    $applyTo = $attribute->getApplyTo();
                    if (count($applyTo) > 0 && !in_array($productTypeId, $applyTo)) {
                        $this->_attributes->removeItemByKey($attribute->getId());
                        break;
                    }
                }
            }
        }

        return $this->_attributes;
    }

    /**
     * Return product ids that not available for selected store
     *
     * @deprecated since 1.4.1
     * @return array
     */
    public function getProductsNotInStoreIds()
    {
        return [];
    }
}
