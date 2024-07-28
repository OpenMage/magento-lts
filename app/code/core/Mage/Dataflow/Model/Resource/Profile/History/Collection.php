<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert history collection
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Profile_History_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('dataflow/profile_history');
    }

    /**
     * Joins admin data to select
     *
     * @return $this
     */
    public function joinAdminUser()
    {
        $this->getSelect()->join(
            ['u' => $this->getTable('admin/user')],
            'u.user_id=main_table.user_id',
            ['firstname', 'lastname']
        );
        return $this;
    }
}
