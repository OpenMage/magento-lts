<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Translation resource model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Translate extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/translate', 'key_id');
    }

    /**
     * Retrieve translation array for store / locale code
     *
     * @param int $storeId
     * @param string|Zend_Locale $locale
     * @return array
     */
    public function getTranslationArray($storeId = null, $locale = null)
    {
        if (!Mage::isInstalled()) {
            return [];
        }

        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $adapter = $this->_getReadAdapter();
        if (!$adapter) {
            return [];
        }

        $select = $adapter->select()
            ->from($this->getMainTable(), ['string', 'translate'])
            ->where('store_id IN (0 , :store_id)')
            ->where('locale = :locale')
            ->order('store_id');

        $bind = [
            ':locale'   => (string) $locale,
            ':store_id' => $storeId,
        ];

        return $adapter->fetchPairs($select, $bind);
    }

    /**
     * Retrieve translations array by strings
     *
     * @param int $storeId
     * @return array
     */
    public function getTranslationArrayByStrings(array $strings, $storeId = null)
    {
        if (!Mage::isInstalled()) {
            return [];
        }

        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $adapter = $this->_getReadAdapter();
        if (!$adapter) {
            return [];
        }

        if (empty($strings)) {
            return [];
        }

        $bind = [
            ':store_id'   => $storeId,
        ];
        $select = $adapter->select()
            ->from($this->getMainTable(), ['string', 'translate'])
            ->where('string IN (?)', $strings)
            ->where('store_id = :store_id');

        return $adapter->fetchPairs($select, $bind);
    }

    /**
     * Retrieve table checksum
     *
     * @return array|false
     */
    public function getMainChecksum()
    {
        return $this->getChecksum($this->getMainTable());
    }
}
