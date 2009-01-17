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
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag resourse model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
        $this->_uniqueFields = array( array('field' => 'name', 'title' => Mage::helper('tag')->__('Tag') ) );
    }

    public function loadByName($model, $name)
    {
        if( $name ) {
            $read = $this->_getReadAdapter();
            $select = $read->select();
            if (Mage::helper('core/string')->strlen($name) > 255) {
                $name = Mage::helper('core/string')->substr($name, 0, 255);
            }

            $select->from($this->getMainTable())
                ->where('name = ?', $name);
            $data = $read->fetchRow($select);

            $model->setData( ( is_array($data) ) ? $data : array() );
        } else {
            return false;
        }
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId() && $object->getStatus()==$object->getApprovedStatus()) {
            $searchTag = new Varien_Object();
            $this->loadByName($searchTag, $object->getName());
            if($searchTag->getData($this->getIdFieldName()) && $searchTag->getStatus()==$object->getPendingStatus()) {
                $object->setId($searchTag->getData($this->getIdFieldName()));
            }
        }

        if (Mage::helper('core/string')->strlen($object->getName()) > 255) {
            $object->setName(Mage::helper('core/string')->substr($object->getName(), 0, 255));
        }

        return parent::_beforeSave($object);
    }

    public function aggregate($object)
    {
        $selectLocal = $this->_getReadAdapter()->select()
            ->from(
                array('main'  => $this->getTable('relation')),
                array(
                    'customers'=>'COUNT(DISTINCT main.customer_id)',
                    'products'=>'COUNT(DISTINCT main.product_id)',
                    'store_id',
                    'uses'=>'COUNT(main.tag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $object->getId())
            ->where('main.active')
            ->group('main.store_id');

        $selectGlobal = $this->_getReadAdapter()->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array(
                    'customers'=>'COUNT(DISTINCT main.customer_id)',
                    'products'=>'COUNT(DISTINCT main.product_id)',
                    'store_id'=>'( 0 )' /* Workaround*/,
                    'uses'=>'COUNT(main.tag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $object->getId())
            ->where('main.active');

        $selectHistorical = $this->_getReadAdapter()->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array('historical_uses'=>'COUNT(main.tag_relation_id)',
                'store_id')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->group('main.store_id')
            ->where('main.tag_id = ?', $object->getId());

       $selectHistoricalGlobal = $this->_getReadAdapter()->select()
            ->from(
                array('main'=>$this->getTable('relation')),
                array('historical_uses'=>'COUNT(main.tag_relation_id)')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.website_id=store.website_id AND product_website.product_id=main.product_id',
                array()
            )
            ->where('main.tag_id = ?', $object->getId());

        $historicalAll = $this->_getReadAdapter()->fetchAll($selectHistorical);
        $historicalCache = array();
        foreach ($historicalAll as $historical) {
            $historicalCache[$historical['store_id']] = $historical['historical_uses'];
        }

        $summaries = $this->_getReadAdapter()->fetchAll($selectLocal);
        if ($row = $this->_getReadAdapter()->fetchRow($selectGlobal)) {
            $historical = $this->_getReadAdapter()->fetchOne($selectHistoricalGlobal);

            if($historical) {
                $row['historical_uses'] = $historical;
            }

            $summaries[] = $row;
        }

        $this->_getReadAdapter()->delete($this->getTable('summary'), $this->_getReadAdapter()->quoteInto('tag_id = ?', $object->getId()));

        foreach ($summaries as $summary) {
            if(!isset($summary['historical_uses'])) {
                $summary['historical_uses'] = isset($historicalCache[$summary['store_id']]) ? $historicalCache[$summary['store_id']] : 0;
            }
            $summary['tag_id'] = $object->getId();
            $summary['popularity'] = $summary['historical_uses'];
            if (is_null($summary['uses'])) {
                $summary['uses'] = 0;
            }

            $this->_getReadAdapter()->insert($this->getTable('summary'), $summary);
        }

        return $object;
    }

    public function addSummary($object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('summary'))
            ->where('tag_id = ?', $object->getId())
            ->where('store_id = ?', $object->getStoreId());

        $row = $this->_getReadAdapter()->fetchAll($select);

        $object->addData($row);
        return $object;
    }
}