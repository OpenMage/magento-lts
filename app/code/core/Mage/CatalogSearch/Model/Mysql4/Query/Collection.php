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
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search query collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Mysql4_Query_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogsearch/query');
    }

    public function setQueryFilter($query)
    {
    	$this->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
    		->from(
    			array('main_table'=>$this->getTable('catalogsearch/search_query')),
    			array('query'=>"if(ifnull(synonim_for,'')<>'', synonim_for, query_text)", 'num_results')
    		)
    		->where('num_results>0 and display_in_terms=1 and query_text like ?', $query.'%')
    		->order('popularity desc');
		return $this;
    }

    public function setPopularQueryFilter($storeIds = null)
    {
    	$this->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
    		->from(
    			array('main_table'=>$this->getTable('catalogsearch/search_query')),
    			array('name'=>"if(ifnull(synonim_for,'')<>'', synonim_for, query_text)", 'num_results')
    		);
        if ($storeIds) {
            $this->getSelect()->where('num_results>0 and store_id in (?)', $storeIds);
        } else if ($storeIds === null){
    		$this->getSelect()->where('num_results>0 and store_id=?',Mage::app()->getStore()->getId());
        }

        $this->getSelect()->order(array('popularity desc','name'));

		return $this;
    }

    public function setRecentQueryFilter()
    {
    	$this->setOrder('updated_at', 'desc');
		return $this;
    }
}
