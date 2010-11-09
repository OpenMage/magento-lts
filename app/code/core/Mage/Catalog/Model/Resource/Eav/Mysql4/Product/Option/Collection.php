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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product options collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_option');
    }

    public function getOptions($store_id)
    {
        $this->getSelect()
            ->joinLeft(array('default_option_price'=>$this->getTable('catalog/product_option_price')),
                '`default_option_price`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`default_option_price`.store_id=?',0),
                array('default_price'=>'price','default_price_type'=>'price_type'))
            ->joinLeft(array('store_option_price'=>$this->getTable('catalog/product_option_price')),
                '`store_option_price`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_price`.store_id=?', $store_id),
                array('store_price'=>'price','store_price_type'=>'price_type',
                'price'=>new Zend_Db_Expr('IFNULL(`store_option_price`.price,`default_option_price`.price)'),
                'price_type'=>new Zend_Db_Expr('IFNULL(`store_option_price`.price_type,`default_option_price`.price_type)')))
            ->join(array('default_option_title'=>$this->getTable('catalog/product_option_title')),
                '`default_option_title`.option_id=`main_table`.option_id',
                array('default_title'=>'title'))
            ->joinLeft(array('store_option_title'=>$this->getTable('catalog/product_option_title')),
                '`store_option_title`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_title`.store_id=?', $store_id),
                array('store_title'=>'title',
                'title'=>new Zend_Db_Expr('IFNULL(`store_option_title`.title,`default_option_title`.title)')))
            ->where('`default_option_title`.store_id=?', 0);

        return $this;
    }

    public function addTitleToResult($store_id)
    {
        $this->getSelect()
            ->join(array('default_option_title'=>$this->getTable('catalog/product_option_title')),
                '`default_option_title`.option_id=`main_table`.option_id',
                array('default_title'=>'title'))
            ->joinLeft(array('store_option_title'=>$this->getTable('catalog/product_option_title')),
                '`store_option_title`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_title`.store_id=?', $store_id),
                array('store_title'=>'title',
                'title'=>new Zend_Db_Expr('IFNULL(`store_option_title`.title,`default_option_title`.title)')))
            ->where('`default_option_title`.store_id=?', 0);

        return $this;
    }

    public function addPriceToResult($store_id)
    {
        $this->getSelect()
            ->joinLeft(array('default_option_price'=>$this->getTable('catalog/product_option_price')),
                '`default_option_price`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`default_option_price`.store_id=?',0),
                array('default_price'=>'price','default_price_type'=>'price_type'))
            ->joinLeft(array('store_option_price'=>$this->getTable('catalog/product_option_price')),
                '`store_option_price`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_price`.store_id=?', $store_id),
                array('store_price'=>'price','store_price_type'=>'price_type',
                'price'=>new Zend_Db_Expr('IFNULL(`store_option_price`.price,`default_option_price`.price)'),
                'price_type'=>new Zend_Db_Expr('IFNULL(`store_option_price`.price_type,`default_option_price`.price_type)')));
        return $this;
    }

    public function addValuesToResult($storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $optionIds = array();
        foreach ($this as $option) {
            $optionIds[] = $option->getId();
        }
        if (!empty($optionIds)) {
            $values = Mage::getModel('catalog/product_option_value')
                ->getCollection()
                ->addTitleToResult($storeId)
                ->addPriceToResult($storeId)
                ->addOptionToFilter($optionIds)
                ->setOrder('sort_order', 'asc')
                ->setOrder('title', 'asc');

            foreach ($values as $value) {
                if($this->getItemById($value->getOptionId())) {
                    $this->getItemById($value->getOptionId())->addValue($value);
                    $value->setOption($this->getItemById($value->getOptionId()));
                }
            }
        }

        return $this;
    }

    public function addProductToFilter($product)
    {
        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif (is_array($product)) {
            $this->addFieldToFilter('product_id', array('in' => $product));
        } elseif ($product instanceof Mage_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', $product->getId());
        } else {
            $this->addFieldToFilter('product_id', $product);
        }

        return $this;
    }

    /**
     * Add filtering by option ids
     *
     * @param mixed $optionIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection
     */
    public function addIdsToFilter($optionIds)
    {
        $this->addFieldToFilter('main_table.option_id', $optionIds);
        return $this;
    }

    /**
     * Call of protected method reset
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection
     */
    public function reset() {
        return $this->_reset();
    }

}
