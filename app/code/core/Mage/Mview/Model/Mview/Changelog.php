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
 * @package     Mage_Mview
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Mview_Model_Mview_Changelog
 *
 * @category    Mage
 * @package     Mage_Mview
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Mview_Model_Mview_Changelog extends Mage_Mview_Model_Abstract
{
    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('mview/mview_changelog');
    }

    /**
     * Returns collection of active changelogs
     *
     * @return object
     */
    public function getCollection()
    {
        $collection = parent::getCollection();
        if ($this->getMviewId()) {
            $collection->addFieldToFilter('mview_id', $this->getMviewId());
        }
        return $collection;
    }

    /**
     * Returns changelog id for table
     *
     * @param $logTable
     * @return int|null
     * @throws Exception
     */
    public function getIdByTableName($logTable)
    {
        if (!$this->getMviewId() || !$logTable) {
            throw new Exception('Cann\'t retrieve id, because mview_id isn\'t set!!');
        }
        return $this->getResource()->getIdByTableName($this->getMviewId(), $logTable);
    }

    /**
     * Enable changelog for table
     *
     * @param $logTable
     * @param $logColumn
     * @return Mage_Mview_Model_Mview_Changelog
     * @throws Exception
     */
    public function enable($logTable, $logColumn)
    {
        if ($this->getIdByTableName($logTable)) {
            throw new Exception('Changelog for this table already exists!!');
        }
        $this->setLogTable($logTable)
            ->setLogColumn($logColumn)
            ->save();
        return $this;
    }

    /**
     * Disable changelog for table
     *
     * @param $logTable
     * @return Mage_Mview_Model_Mview_Changelog
     * @throws Exception
     */
    public function disable($logTable)
    {
        $id = $this->getIdByTableName($logTable);
        if (!$id) {
            throw new Exception('Changelog for this table doesn\'t exists!!');
        }
        $this->load($id)
            ->delete();
        return $this;
    }
}

