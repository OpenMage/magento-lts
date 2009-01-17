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
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Tax_Model_Rule extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/rule');
    }

    protected function _beforeSave()
    {
        $this->cleanCache();
        parent::_beforeSave();
    }

    protected function _beforeDelete()
    {
        $this->cleanCache();
        parent::_beforeDelete();
    }

    public function cleanCache()
    {
        $ids = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('tax_class_id', $this->getTaxProductClassId())
            ->getAllIds();
        $tags = array();
        foreach ($ids as $id) {
            $tags[] = 'catalog_product_'.$id;
        }
        Mage::app()->cleanCache($tags);
    }
//    public function __construct($rule=false)
//    {
//        parent::__construct();
//        $this->setIdFieldName($this->getResource()->getIdFieldName());
//    }
//
//    public function getResource()
//    {
//        return Mage::getResourceModel('tax/rule');
//    }
//
//    public function load($ruleId)
//    {
//        $this->getResource()->load($this, $ruleId);
//        return $this;
//    }
//
//    public function save()
//    {
//        $this->getResource()->save($this);
//        return $this;
//    }
//
//    public function delete()
//    {
//        $this->getResource()->delete($this);
//        return $this;
//    }
}