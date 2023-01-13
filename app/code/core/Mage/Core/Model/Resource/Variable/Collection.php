<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom variabel collection
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Variable_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store Id
     *
     * @var int
     */
    protected $_storeId    = 0;

    /**
     *  Define resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('core/variable');
    }

    /**
     * Setter
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Add store values to result
     *
     * @return $this
     */
    public function addValuesToResult()
    {
        $this->getSelect()
            ->join(
                ['value_table' => $this->getTable('core/variable_value')],
                'value_table.variable_id = main_table.variable_id',
                ['value_table.plain_value', 'value_table.html_value']
            );
        $this->addFieldToFilter('value_table.store_id', ['eq' => $this->getStoreId()]);
        return $this;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('code', 'name');
    }
}
