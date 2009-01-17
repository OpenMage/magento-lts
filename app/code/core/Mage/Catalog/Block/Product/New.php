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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * New products block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_New extends Mage_Catalog_Block_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();

        $storeId    = Mage::app()->getStore()->getId();

        $product    = Mage::getModel('catalog/product');
        /* @var $product Mage_Catalog_Model_Product */
        $todayDate  = $product->getResource()->formatDate(time());
        $products   = $product->setStoreId($storeId)->getCollection()
            ->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $todayDate))
            ->addAttributeToFilter(array(array('attribute'=>'news_to_date', 'date'=>true, 'from'=>$todayDate), array('attribute'=>'news_to_date', 'is' => new Zend_Db_Expr('null'))),'','left')
            ->addAttributeToSort('news_from_date','desc')
            ->addAttributeToSelect(array('name', 'price', 'small_image'), 'inner')
            ->addAttributeToSelect(array('special_price', 'special_from_date', 'special_to_date'), 'left')
        ;
        /* @var $products Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        $products->setOrder('news_from_date')->setPageSize(5)->setCurPage(1);

        $this->setProductCollection($products);
    }
}