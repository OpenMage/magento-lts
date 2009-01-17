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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Translation resource model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_Translate extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/translate', 'key_id');
    }

    public function getTranslationArray($storeId=null)
    {
        if(!Mage::isInstalled()) {
            return array();
        }

        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $read = $this->_getReadAdapter();
        if (!$read) {
            return array();
        }

//        $select = $read->select()
//            ->from(array('main'=>$this->getMainTable()), array(
//                    'string',
//                    new Zend_Db_Expr('IFNULL(store.translate, main.translate)')
//                ))
//            ->joinLeft(array('store'=>$this->getMainTable()),
//                $read->quoteInto('store.string=main.string AND store.store_id=?', $storeId),
//                'string')
//            ->where('main.store_id=0');
//
//        $result = $read->fetchPairs($select);
//
        $select = $read->select()
            ->from($this->getMainTable())
            ->where('store_id in (?)', array(0, $storeId))
            ->order('store_id');

        $result = array();
        foreach ($read->fetchAll($select) as $row) {
            $result[$row['string']] = $row['translate'];
        }

        return $result;
    }

    public function getTranslationArrayByStrings(array $strings, $storeId=null)
    {
        if(!Mage::isInstalled()) {
            return array();
        }

        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $read = $this->_getReadAdapter();
        if (!$read) {
            return array();
        }

        if (empty($strings)) {
            return array();
        }

        $select = $read->select()
            ->from($this->getMainTable())
            ->where('string in (:tr_strings)')
            ->where('store_id = ?', $storeId);
        $result = array();
        foreach ($read->fetchAll($select, array('tr_strings'=>$read->quote($strings))) as $row) {
            $result[$row['string']] = $row['translate'];
        }

        return $result;
    }

    public function getMainChecksum()
    {
        return parent::getChecksum($this->getMainTable());
    }
}
