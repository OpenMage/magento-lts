<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Resource model for manipulate system variables
 *
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_Variable extends Mage_Core_Model_Resource_Db_Abstract
{
    public const CACHE_ID = 'permission_variable';

    protected function _construct()
    {
        $this->_init('admin/permission_variable', 'variable_id');
    }

    protected function _generateCache()
    {
        /** @var Mage_Admin_Model_Resource_Variable_Collection $collection */
        $collection = Mage::getResourceModel('admin/variable_collection');
        $collection->addFieldToFilter('is_allowed', ['eq' => 1]);
        $data = $collection->getColumnValues('variable_name');
        $data = array_flip($data);
        Mage::app()->saveCache(
            Mage::helper('core')->jsonEncode($data),
            self::CACHE_ID,
            [Mage_Core_Model_Resource_Db_Collection_Abstract::CACHE_TAG],
        );
    }

    /**
     * Get allowed types
     */
    public function getAllowedPaths()
    {
        $data = Mage::app()->getCacheInstance()->load(self::CACHE_ID);
        if ($data === false) {
            $this->_generateCache();
            $data = Mage::app()->getCacheInstance()->load(self::CACHE_ID);
        }
        return Mage::helper('core')->jsonDecode($data);
    }

    /**
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_generateCache();
        return parent::_afterSave($object);
    }

    /**
     * @inheritDoc
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $this->_generateCache();
        return parent::_afterDelete($object);
    }
}
