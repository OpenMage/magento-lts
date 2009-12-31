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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Mysql4_Cache extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/cache_option', 'code');
    }

    /**
     * Get all cache options
     * @return array | false
     */
    public function getAllOptions()
    {
        $adapter = $this->_getReadAdapter();
        if ($adapter) {
            /**
             * Check if table exist (it protect upgrades. cache settings checked before upgrades)
             */
            if ($adapter->fetchOne('SHOW TABLES LIKE ?', $this->getMainTable())) {
                $select = $adapter->select()
                    ->from($this->getMainTable(), array('code', 'value'));
                return $adapter->fetchPairs($select);
            }
        }
        return false;
    }

    /**
     * Save all options to option table
     *
     * @param   array $options
     * @return  Mage_Core_Model_Mysql4_Cache
     */
    public function saveAllOptions($options)
    {
        if (!$this->_getWriteAdapter()) {
            return $this;
        }
        $this->_getWriteAdapter()->delete($this->getMainTable());
        foreach ($options as $code => $value) {
            $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                'code'  => $code,
                'value' => $value
            ));
        }
        return $this;
    }
}
