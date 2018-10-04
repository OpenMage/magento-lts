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
 * @package     Mage_Tag
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customers collection
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Entity_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected $_tagTable;
    protected $_tagRelTable;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct();
        $this->_tagTable = $resource->getTableName('tag/tag');
        $this->_tagRelTable = $resource->getTableName('tag/tag_relation');

//        $this->joinField('tag_total_used', $this->_tagRelTable, 'count(_table_tag_total_used.tag_relations_id)', 'entity_val_id=entity_id', array('entity_id' => '2'));
//        $this->getSelect()->group('tag_tag_id');
//        echo $this->getSelect();
//        $this->_productTable = $resource->getTableName('catalog/product');
//        $this->_select->from(array('p' => $this->_productTable))
//            ->join(array('tr' => $this->_tagRelTable), 'tr.entity_val_id=p.product_id and tr.entity_id=1', array('total_used' => 'count(tr.tag_relations_id)'))
//            ->group('p.product_id', 'tr.tag_id')
//        ;

    }

    public function addTagFilter($tagId)
    {
        $this->joinField('tag_tag_id', $this->_tagRelTable, 'tag_id', 'customer_id=entity_id');
        $this->getSelect()->where($this->_getAttributeTableAlias('tag_tag_id') . '.tag_id=?', $tagId);
        return $this;
    }

    public function addProductFilter($productId)
    {
        $this->joinField('tag_product_id', $this->_tagRelTable, 'product_id', 'customer_id=entity_id');
        $this->getSelect()->where($this->_getAttributeTableAlias('tag_product_id') . '.product_id=?', $productId);
        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        $this->_loadTags($printQuery, $logQuery);
        return $this;
    }

    protected function _loadTags($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items)) {
            return $this;
        }
        $customerIds = array();
        foreach ($this->getItems() as $item) {
            $customerIds[] = $item->getId();
        }
        $this->getSelect()->reset()
            ->from(array('tr' => $this->_tagRelTable), array('*','total_used' => 'count(tr.tag_relation_id)'))
            ->joinLeft(array('t' => $this->_tagTable),'t.tag_id=tr.tag_id')
            ->group(array('tr.customer_id', 't.tag_id'))
            ->where('tr.customer_id in (?)',$customerIds)
        ;
        $this->printLogQuery($printQuery, $logQuery);

        $tags = array();
        $data = $this->_read->fetchAll($this->getSelect());
        foreach ($data as $row) {
            if (!isset($tags[ $row['customer_id'] ])) {
                $tags[ $row['customer_id'] ] = array();
            }
            $tags[ $row['customer_id'] ][] = $row;
        }
        foreach ($this->getItems() as $item) {
            if (isset($tags[$item->getId()])) {
                $item->setData('tags', $tags[$item->getId()]);
            }
        }
        return $this;
    }

}
