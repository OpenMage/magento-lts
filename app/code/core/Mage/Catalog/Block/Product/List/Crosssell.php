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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product related items block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Catalog_Block_Product_List_Crosssell extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Crosssell item collection
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
     */
    protected $_itemCollection;

    /**
     * Prepare crosssell items data
     *
     * @return $this
     */
    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        $this->_itemCollection = $product->getCrossSellProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * Before rendering html process
     * Prepare items collection
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve crosssell items collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
     */
    public function getItems()
    {
        return $this->_itemCollection;
    }

}
