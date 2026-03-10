<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * OAuth token resource collection model
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Resource_Token_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('oauth/token');
    }

    /**
     * Load collection with consumer data
     *
     * Method use for show applications list (token-consumer)
     *
     * @return $this
     */
    public function joinConsumerAsApplication()
    {
        $select = $this->getSelect();
        $select->joinLeft(
            ['c' => $this->getTable('oauth/consumer')],
            'c.entity_id = main_table.consumer_id',
            'name',
        );

        return $this;
    }

    /**
     * Add filter by admin ID
     *
     * @param  int   $adminId
     * @return $this
     */
    public function addFilterByAdminId($adminId)
    {
        $this->addFilter('main_table.admin_id', $adminId);
        return $this;
    }

    /**
     * Add filter by customer ID
     *
     * @param  int   $customerId
     * @return $this
     */
    public function addFilterByCustomerId($customerId)
    {
        $this->addFilter('main_table.customer_id', $customerId);
        return $this;
    }

    /**
     * Add filter by consumer ID
     *
     * @param  int   $consumerId
     * @return $this
     */
    public function addFilterByConsumerId($consumerId)
    {
        $this->addFilter('main_table.consumer_id', $consumerId);
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param  string $type
     * @return $this
     */
    public function addFilterByType($type)
    {
        $this->addFilter('main_table.type', $type);
        return $this;
    }

    /**
     * Add filter by ID
     *
     * @param  array|int $id
     * @return $this
     */
    public function addFilterById($id)
    {
        $this->addFilter('main_table.entity_id', ['in' => $id], 'public');
        return $this;
    }

    /**
     * Add filter by "Is Revoked" status
     *
     * @param  bool|int $flag
     * @return $this
     */
    public function addFilterByRevoked($flag)
    {
        $this->addFilter('main_table.revoked', (int) $flag, 'public');
        return $this;
    }
}
