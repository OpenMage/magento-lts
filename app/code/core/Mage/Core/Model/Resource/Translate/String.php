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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * String translate resource model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Translate_String extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core/translate', 'key_id');
    }

    /**
     * Load
     *
     * @param Mage_Core_Model_Abstract $object
     * @param String $value
     * @param String $field
     * @return array
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (is_string($value)) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable().'.string=:tr_string');
            $result = $this->_getReadAdapter()->fetchRow($select, array('tr_string'=>$value));
            $object->setData($result);
            $this->_afterLoad($object);
            return $result;
        } else {
            return parent::load($object, $value, $field);
        }
    }

    /**
     * Retrieve select for load
     *
     * @param String $field
     * @param String $value
     * @param Mage_Core_Model_Abstract $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->where('store_id = ?', Mage_Core_Model_App::ADMIN_STORE_ID);
        return $select;
    }

    /**
     * After translation loading
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('store_id', 'translate'))
            ->where('string = :translate_string');
        $translations = $adapter->fetchPairs($select, array('translate_string' => $object->getString()));
        $object->setStoreTranslations($translations);
        return parent::_afterLoad($object);
    }

    /**
     * Before save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Translate_String
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'key_id')
            ->where('string = :string')
            ->where('store_id = :store_id');

        $bind = array(
            'string'   => $object->getString(),
            'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID
        );

        $object->setId($adapter->fetchOne($select, $bind));
        return parent::_beforeSave($object);
    }

    /**
     * After save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Translate_String
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('store_id', 'key_id'))
            ->where('string = :string');
        $stores = $adapter->fetchPairs($select, array('string' => $object->getString()));

        $translations = $object->getStoreTranslations();

        if (is_array($translations)) {
            foreach ($translations as $storeId => $translate) {
                if (is_null($translate) || $translate=='') {
                     $where = array(
                        'store_id = ?'    => $storeId,
                        'string = ?'      => $object->getString()
                    );
                    $adapter->delete($this->getMainTable(), $where);
                } else {
                    $data = array(
                       'store_id'  => $storeId,
                       'string'    => $object->getString(),
                       'translate' => $translate,
                    );

                    if (isset($stores[$storeId])) {
                        $adapter->update(
                           $this->getMainTable(),
                           $data,
                           array('key_id = ?' => $stores[$storeId]));
                    } else {
                        $adapter->insert($this->getMainTable(), $data);
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
     * @return Mage_Core_Model_Resource_Translate_String
     */
    public function deleteTranslate($string, $locale = null, $storeId = null)
    {
        if (is_null($locale)) {
            $locale = Mage::app()->getLocale()->getLocaleCode();
        }

        $where = array(
            'locale = ?' => $locale,
            'string = ?' => $string
        );

        if ($storeId === false) {
            $where['store_id > ?'] = Mage_Core_Model_App::ADMIN_STORE_ID;
        } elseif ($storeId !== null) {
            $where['store_id = ?'] = $storeId;
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Save translation
     *
     * @param String $string
     * @param String $translate
     * @param String $locale
     * @param int|null $storeId
     * @return Mage_Core_Model_Resource_Translate_String
     */
    public function saveTranslate($string, $translate, $locale = null, $storeId = null)
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
            ->where('store_id = :store_id')
            ->where('locale = :locale')
            ->where('string = :string')
            ->where('crc_string = :crc_string');
        $bind = array(
            'store_id'   => $storeId,
            'locale'     => $locale,
            'string'     => $string,
            'crc_string' => crc32($string),
        );

        if ($row = $write->fetchRow($select, $bind)) {
            $original = $string;
            if (strpos($original, '::') !== false) {
                list($scope, $original) = explode('::', $original);
            }
            if ($original == $translate) {
                $write->delete($table, array('key_id=?' => $row['key_id']));
            } elseif ($row['translate'] != $translate) {
                $write->update($table, array('translate' => $translate), array('key_id=?' => $row['key_id']));
            }
        } else {
            $write->insert($table, array(
                'store_id'   => $storeId,
                'locale'     => $locale,
                'string'     => $string,
                'translate'  => $translate,
                'crc_string' => crc32($string),
            ));
        }

        return $this;
    }
}
