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
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Review summery collection
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Resource_Review_Summary_Collection extends Varien_Data_Collection_Db
{
    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_summaryTable;

    /**
     * Enter description here ...
     *
     */
    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');
        $this->_setIdFieldName('primary_id');

        parent::__construct($resources->getConnection('review_read'));
        $this->_summaryTable = $resources->getTableName('review/review_aggregate');

        $this->_select->from($this->_summaryTable);

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('review/review_summary'));
    }

    /**
     * Add entity filter
     *
     * @param unknown_type $entityId
     * @param unknown_type $entityType
     * @return $this
     */
    public function addEntityFilter($entityId, $entityType = 1)
    {
        $this->_select->where('entity_pk_value IN(?)', $entityId)
            ->where('entity_type = ?', $entityType);
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->_select->where('store_id = ?', $storeId);
        return $this;
    }
}
