<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget Instance Collection
 *
 * @category   Mage
 * @package    Mage_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Model_Resource_Widget_Instance_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Fields map for corellation names & real selected fields
     *
     * @var array
     */
    protected $_map = ['fields' => ['type' => 'instance_type']];

    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('widget/widget_instance');
    }

    /**
     * Filter by store ids
     *
     * @param array|integer $storeIds
     * @param boolean $withDefaultStore if TRUE also filter by store id '0'
     * @return $this
     */
    public function addStoreFilter($storeIds = [], $withDefaultStore = true)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        if ($withDefaultStore && !in_array('0', $storeIds)) {
            array_unshift($storeIds, 0);
        }
        $where = [];
        foreach ($storeIds as $storeId) {
            $where[] = $this->_getConditionSql('store_ids', ['finset' => $storeId]);
        }

        $this->_select->where(implode(' OR ', $where));

        return $this;
    }
}
