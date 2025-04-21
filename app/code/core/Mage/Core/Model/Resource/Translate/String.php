<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * String translate resource model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Translate_String extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/translate', 'key_id');
    }

    /**
     * @param Mage_Core_Model_Translate_String $object
     * @inheritDoc
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (is_string($value)) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable() . '.string=:tr_string');
            $result = $this->_getReadAdapter()->fetchRow($select, ['tr_string' => $value]);
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
     * @param Mage_Core_Model_Translate_String $object
     * @inheritDoc
     */
    public function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['store_id', 'translate'])
            ->where('string = :translate_string');
        $translations = $adapter->fetchPairs($select, ['translate_string' => $object->getString()]);
        $object->setStoreTranslations($translations);
        return parent::_afterLoad($object);
    }

    /**
     * @param Mage_Core_Model_Translate_String $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'key_id')
            ->where('string = :string')
            ->where('store_id = :store_id');

        $bind = [
            'string'   => $object->getString(),
            'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID,
        ];

        $object->setId($adapter->fetchOne($select, $bind));
        return parent::_beforeSave($object);
    }

    /**
     * @param Mage_Core_Model_Translate_String $object
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['store_id', 'key_id'])
            ->where('string = :string');
        $stores = $adapter->fetchPairs($select, ['string' => $object->getString()]);

        $translations = $object->getStoreTranslations();

        if (is_array($translations)) {
            foreach ($translations as $storeId => $translate) {
                if (is_null($translate) || $translate == '') {
                    $where = [
                        'store_id = ?'    => $storeId,
                        'string = ?'      => $object->getString(),
                    ];
                    $adapter->delete($this->getMainTable(), $where);
                } else {
                    $data = [
                        'store_id'  => $storeId,
                        'string'    => $object->getString(),
                        'translate' => $translate,
                    ];

                    if (isset($stores[$storeId])) {
                        $adapter->update(
                            $this->getMainTable(),
                            $data,
                            ['key_id = ?' => $stores[$storeId]],
                        );
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
     * @return $this
     */
    public function deleteTranslate($string, $locale = null, $storeId = null)
    {
        if (is_null($locale)) {
            $locale = Mage::app()->getLocale()->getLocaleCode();
        }

        $where = [
            'locale = ?' => $locale,
            'string = ?' => $string,
        ];

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
     * @return $this
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
            ->from($table, ['key_id', 'translate'])
            ->where('store_id = :store_id')
            ->where('locale = :locale')
            ->where('string = :string')
            ->where('crc_string = :crc_string');
        $bind = [
            'store_id'   => $storeId,
            'locale'     => $locale,
            'string'     => $string,
            'crc_string' => crc32($string),
        ];

        if ($row = $write->fetchRow($select, $bind)) {
            $original = $string;
            if (str_contains($original, '::')) {
                [$scope, $original] = explode('::', $original);
            }
            if ($original == $translate) {
                $write->delete($table, ['key_id=?' => $row['key_id']]);
            } elseif ($row['translate'] != $translate) {
                $write->update($table, ['translate' => $translate], ['key_id=?' => $row['key_id']]);
            }
        } else {
            $write->insert($table, [
                'store_id'   => $storeId,
                'locale'     => $locale,
                'string'     => $string,
                'translate'  => $translate,
                'crc_string' => crc32($string),
            ]);
        }

        return $this;
    }
}
