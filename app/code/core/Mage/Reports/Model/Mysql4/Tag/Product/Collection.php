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
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report Products Tags collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Mysql4_Tag_Product_Collection extends Mage_Tag_Model_Mysql4_Product_Collection
{
    public function addUniqueTagedCount()
    {
        $this->getSelect()
            ->from('', array('utaged' => 'count(DISTINCT(relation.tag_id))'));
            //->order('taged desc');
        return $this;
    }

    public function addAllTagedCount()
    {
        $this->getSelect()
            ->from('', array('taged' => 'count(relation.tag_id)'));
            //->order('taged desc');
        return $this;
    }

    public function addTagedCount()
    {
        $this->getSelect()
            ->from('', array('taged' => 'count(relation.tag_relation_id)'));
            //->order('taged desc');
        return $this;
    }

    public function addGroupByProduct()
    {
        $this->getSelect()
            ->group('relation.product_id');
            $this->setJoinFlag('distinct');
        return $this;
    }

    public function addGroupByTag()
    {
        $this->getSelect()
            ->group('relation.tag_id');
            $this->setJoinFlag('distinct');
        $this->setJoinFlag('group_tag');
        return $this;
    }

    public function addProductFilter($customerId)
    {
        $this->getSelect()
            ->where('relation.product_id = ?', $customerId);
        $this->_customerFilterId = $customerId;
        return $this;
    }



    public function setOrder($attribute, $dir='desc')
    {
        if ($attribute == 'utaged' || $attribute == 'taged' || $attribute == 'tag_name') {
                $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
                parent::setOrder($attribute, $dir);
        }

        return $this;
    }


     protected function _joinFields()
    {
        $tagTable = Mage::getSingleton('core/resource')->getTableName('tag/tag');
        $tagRelationTable = Mage::getSingleton('core/resource')->getTableName('tag/relation');
        $this->addAttributeToSelect('name');
        $this->getSelect()
            ->join(array('relation' => $tagRelationTable), "relation.product_id = e.entity_id")
            ->join(array('t' => $tagTable), "t.tag_id =relation.tag_id", array(
                'tag_id',  'status', 'tag_name' => 'name'
            ));


    }
}
