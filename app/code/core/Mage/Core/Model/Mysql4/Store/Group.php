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
 * Store group resource model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Model_Mysql4_Store_Group extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/store_group', 'group_id');
    }

    protected function _afterSave(Mage_Core_Model_Abstract $model)
    {
        $this->_updateStoreWebsite($model->getId(), $model->getWebsiteId());
        $this->_updateWebsiteDefaultGroup($model->getWebsiteId(), $model->getId());
        $this->_changeWebsite($model);

        return $this;
    }

    protected function _updateWebsiteDefaultGroup($websiteId, $groupId)
    {
        $write = $this->_getWriteAdapter();
        $cnt   = $write->fetchOne($write->select()
            ->from($this->getTable('core/store_group'), array('count'=>'COUNT(*)'))
            ->where($write->quoteInto('website_id=?', $websiteId)),
            'count');
        if ($cnt == 1) {
            $write->update($this->getTable('core/website'),
                array('default_group_id' => $groupId),
                $write->quoteInto('website_id=?', $websiteId)
            );
        }
        return $this;
    }

    protected function _changeWebsite(Mage_Core_Model_Abstract $model) {
        if ($model->getOriginalWebsiteId() && $model->getWebsiteId() != $model->getOriginalWebsiteId()) {
            $write = $this->_getWriteAdapter();
            $groupId = $write->fetchOne($write->select()
                ->from($this->getTable('core/website'), 'default_group_id')
                ->where($write->quoteInto('website_id=?', $model->getOriginalWebsiteId())),
                'default_group_id'
            );
            if ($groupId == $model->getId()) {
                $write->update($this->getTable('core/website'),
                    array('default_group_id'=>0),
                    $write->quoteInto('website_id=?', $model->getOriginalWebsiteId()));
            }
        }
        return $this;
    }

    protected function _updateStoreWebsite($groupId, $websiteId)
    {
        $write = $this->_getWriteAdapter();
        $bind = array('website_id'=>$websiteId);
        $condition = $write->quoteInto('group_id=?', $groupId);
        $this->_getWriteAdapter()->update($this->getTable('core/store'), $bind, $condition);
        return $this;
    }

    protected function _saveDefaultStore($groupId, $storeId)
    {
        $write = $this->_getWriteAdapter();
        $bind = array('default_store_id'=>$storeId);
        $condition = $write->quoteInto('group_id=?', $groupId);
        $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $condition);
        return $this;
    }
}