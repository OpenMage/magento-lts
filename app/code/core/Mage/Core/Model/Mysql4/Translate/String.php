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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * String translate resource model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_Translate_String extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/translate', 'key_id');
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (is_string($value)) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable().'.string=:tr_string');
            $result = $this->_getReadAdapter()->fetchRow($select, array('tr_string'=>$value));
            $object->setData($result);
            $this->_afterLoad($object);
            return $result;
        }
        else {
            return parent::load($object, $value, $field);
        }
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->where('store_id', 0);
        return $select;
    }


    public function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $connection = $this->_getReadAdapter();
        $select = $connection->select()
            ->from($this->getMainTable(), array('store_id', 'translate'))
            ->where('string=:translate_string');
        $translations = $connection->fetchPairs($select, array('translate_string' => $object->getString()));
        $object->setStoreTranslations($translations);
        return parent::_afterLoad($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $connection = $this->_getWriteAdapter();
        $select = $connection->select()
            ->from($this->getMainTable(), 'key_id')
            ->where('string=?', $object->getString())
            ->where('store_id=?', 0);

        $object->setId($connection->fetchOne($select));
        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $connection = $this->_getWriteAdapter();
        $select = $connection->select()
            ->from($this->getMainTable(), array('store_id', 'key_id'))
            ->where('string=?', $object->getString());
        $stors = $connection->fetchPairs($select);

        $translations = $object->getStoreTranslations();

        if (is_array($translations)) {
            foreach ($translations as $storeId => $translate) {
                $condition = $connection->quoteInto('store_id=? AND ', $storeId) .
                    $connection->quoteInto('string=?', $object->getString());

                if (is_null($translate) || $translate=='') {
                    $connection->delete($this->getMainTable(), $condition);
                }
                else {
                    $data = array(
                       'store_id'  => $storeId,
                       'string'    => $object->getString(),
                       'translate' =>$translate,
                    );

                    if (isset($stors[$storeId])) {
                        $connection->update(
                           $this->getMainTable(),
                           $data,
                           $connection->quoteInto('key_id=?', $stors[$storeId]));
                    }
                    else {
                        $connection->insert($this->getMainTable(), $data);
                    }
                }
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Delete translates
     *
     * @param string $string
     * @param string $locale
     * @param int|null $storeId
     *
     * @return Mage_Core_Model_Mysql4_Translate_String
     */
    public function deleteTranslate($string, $locale = null, $storeId = null)
    {
        if (is_null($locale)) {
            $locale = Mage::app()->getLocale()->getLocaleCode();
        }

        $where = array(
            $this->_getWriteAdapter()->quoteInto('locale=?', $locale),
            $this->_getWriteAdapter()->quoteInto('string=?', $string),
        );

        if ($storeId === false) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id>?', 0);
        }
        elseif (!is_null($storeId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    public function saveTranslate($string, $translate, $locale=null, $storeId=null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->getMainTable();

        if (is_null($locale)) {
            $locale = Mage::app()->getLocale()->getLocaleCode();
        }

        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $select = $write->select()
            ->from($table, array('key_id', 'translate'))
            ->where('store_id=?', $storeId)
            ->where('locale=?', $locale)
            ->where('string=?', $string)
        ;
        if ($row = $write->fetchRow($select)) {
            $original = $string;
            if (strpos($original, '::')!==false) {
                list($scope, $original) = explode('::', $original);
            }
            if ($original == $translate) {
                $write->delete($table, 'key_id='.(int)$row['key_id']);
            } elseif ($row['translate']!=$translate) {
                $write->update($table, array('translate'=>$translate), 'key_id='.(int)$row['key_id']);
            }
        } else {
            $write->insert($table, array(
                'store_id'=>$storeId,
                'locale'=>$locale,
                'string'=>$string,
                'translate'=>$translate,
            ));
        }

        return $this;
    }
}
