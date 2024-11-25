<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core Resource Resource Model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Config extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }

    /**
     * Load configuration values into xml config object
     *
     * @param string $condition
     * @return $this
     */
    public function loadToXml(Mage_Core_Model_Config $xmlConfig, $condition = null)
    {
        $read = $this->_getReadAdapter();
        if (!$read) {
            return $this;
        }

        $websites = [];
        $select = $read->select()
            ->from($this->getTable('core/website'), ['website_id', 'code', 'name']);
        $rowset = $read->fetchAssoc($select);
        foreach ($rowset as $w) {
            $xmlConfig->setNode('websites/' . $w['code'] . '/system/website/id', $w['website_id']);
            $xmlConfig->setNode('websites/' . $w['code'] . '/system/website/name', $w['name']);
            $websites[$w['website_id']] = ['code' => $w['code']];
        }

        $stores = [];
        $select = $read->select()
            ->from($this->getTable('core/store'), ['store_id', 'code', 'name', 'website_id'])
            ->order('sort_order ' . Varien_Db_Select::SQL_ASC);
        $rowset = $read->fetchAssoc($select);
        foreach ($rowset as $s) {
            if (!isset($websites[$s['website_id']])) {
                continue;
            }
            $xmlConfig->setNode('stores/' . $s['code'] . '/system/store/id', $s['store_id']);
            $xmlConfig->setNode('stores/' . $s['code'] . '/system/store/name', $s['name']);
            $xmlConfig->setNode('stores/' . $s['code'] . '/system/website/id', $s['website_id']);
            $xmlConfig->setNode('websites/' . $websites[$s['website_id']]['code'] . '/system/stores/' . $s['code'], $s['store_id']);
            $stores[$s['store_id']] = ['code' => $s['code']];
            $websites[$s['website_id']]['stores'][$s['store_id']] = $s['code'];
        }

        $substFrom = [];
        $substTo   = [];

        // load all configuration records from database, which are not inherited
        $select = $read->select()
            ->from($this->getMainTable(), ['scope', 'scope_id', 'path', 'value']);
        if (!is_null($condition)) {
            $select->where($condition);
        }
        $rowset = $read->fetchAll($select);

        // set default config values from database
        foreach ($rowset as $r) {
            if ($r['scope'] !== 'default') {
                continue;
            }
            $value = str_replace($substFrom, $substTo, (string) $r['value']);
            $xmlConfig->setNode('default/' . $r['path'], $value);
        }

        // inherit default config values to all websites
        $extendSource = $xmlConfig->getNode('default');
        foreach ($websites as $id => $w) {
            $websiteNode = $xmlConfig->getNode('websites/' . $w['code']);
            $websiteNode->extend($extendSource);
        }

        $deleteWebsites = [];
        // set websites config values from database
        foreach ($rowset as $r) {
            if ($r['scope'] !== 'websites') {
                continue;
            }
            $value = str_replace($substFrom, $substTo, (string) $r['value']);
            if (isset($websites[$r['scope_id']])) {
                $nodePath = sprintf('websites/%s/%s', $websites[$r['scope_id']]['code'], $r['path']);
                $xmlConfig->setNode($nodePath, $value);
            } else {
                $deleteWebsites[$r['scope_id']] = $r['scope_id'];
            }
        }

        // extend website config values to all associated stores
        foreach ($websites as $website) {
            $extendSource = $xmlConfig->getNode('websites/' . $website['code']);
            if (isset($website['stores'])) {
                foreach ($website['stores'] as $sCode) {
                    $storeNode = $xmlConfig->getNode('stores/' . $sCode);
                    /**
                     * $extendSource DO NOT need overwrite source
                     */
                    $storeNode->extend($extendSource, false);
                }
            }
        }

        $deleteStores = [];
        // set stores config values from database
        foreach ($rowset as $r) {
            if ($r['scope'] !== 'stores') {
                continue;
            }
            $value = str_replace($substFrom, $substTo, (string) $r['value']);
            if (isset($stores[$r['scope_id']])) {
                $nodePath = sprintf('stores/%s/%s', $stores[$r['scope_id']]['code'], $r['path']);
                $xmlConfig->setNode($nodePath, $value);
            } else {
                $deleteStores[$r['scope_id']] = $r['scope_id'];
            }
        }

        if ($deleteWebsites) {
            $this->_getWriteAdapter()->delete($this->getMainTable(), [
                'scope = ?'      => 'websites',
                'scope_id IN(?)' => $deleteWebsites,
            ]);
        }

        if ($deleteStores) {
            $this->_getWriteAdapter()->delete($this->getMainTable(), [
                'scope=?'        => 'stores',
                'scope_id IN(?)' => $deleteStores,
            ]);
        }
        return $this;
    }

    /**
     * Save config value
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return $this
     */
    public function saveConfig($path, $value, $scope, $scopeId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $select = $writeAdapter->select()
            ->from($this->getMainTable())
            ->where('path = ?', $path)
            ->where('scope = ?', $scope)
            ->where('scope_id = ?', $scopeId);
        $row = $writeAdapter->fetchRow($select);

        $newData = [
            'scope'     => $scope,
            'scope_id'  => $scopeId,
            'path'      => $path,
            'value'     => $value,
        ];

        if ($row) {
            $whereCondition = [$this->getIdFieldName() . '=?' => $row[$this->getIdFieldName()]];
            $writeAdapter->update($this->getMainTable(), $newData, $whereCondition);
        } else {
            $writeAdapter->insert($this->getMainTable(), $newData);
        }
        return $this;
    }

    /**
     * Delete config value
     *
     * @param string $path
     * @param string $scope
     * @param int $scopeId
     * @return $this
     */
    public function deleteConfig($path, $scope, $scopeId)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getMainTable(), [
            $adapter->quoteInto('path = ?', $path),
            $adapter->quoteInto('scope = ?', $scope),
            $adapter->quoteInto('scope_id = ?', $scopeId),
        ]);
        return $this;
    }

    /**
     * Get config value
     *
     * @return string|false
     */
    public function getConfig(string $path, string $scope, int $scopeId)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getMainTable(), 'value')
            ->where('path = ?', $path)
            ->where('scope = ?', $scope)
            ->where('scope_id = ?', $scopeId);

        return $readAdapter->fetchOne($select);
    }
}
