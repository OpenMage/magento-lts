<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
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
            ['firstname', 'lastname'],
        );
        return $this;
    }
}
