<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customers collection
 *
 * @package    Mage_Customer
 *
 * @method Mage_Customer_Model_Customer[] getItems()
 */
class Mage_Customer_Model_Resource_Customer_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/customer');
    }

    /**
     * Group result by customer email
     *
     * @return $this
     */
    public function groupByEmail()
    {
        $this->getSelect()
            ->from(
                ['email' => $this->getEntity()->getEntityTable()],
                ['email_count' => new Zend_Db_Expr('COUNT(email.entity_id)')],
            )
            ->where('email.entity_id = e.entity_id')
            ->group('email.email');

        return $this;
    }

    /**
     * Add Name to select
     *
     * @return $this
     */
    public function addNameToSelect()
    {
        $fields = [];
        $customerAccount = Mage::getConfig()->getFieldset('customer_account');
        foreach ($customerAccount as $code => $node) {
            if ($node->is('name')) {
                $fields[$code] = $code;
            }
        }

        $adapter = $this->getConnection();
        $concatenate = [];
        if (isset($fields['prefix'])) {
            $concatenate[] = $adapter->getCheckSql(
                '{{prefix}} IS NOT NULL AND {{prefix}} != \'\'',
                $adapter->getConcatSql(['LTRIM(RTRIM({{prefix}}))', '\' \'']),
                '\'\'',
            );
        }
        $concatenate[] = 'LTRIM(RTRIM({{firstname}}))';
        $concatenate[] = '\' \'';
        if (isset($fields['middlename'])) {
            $concatenate[] = $adapter->getCheckSql(
                '{{middlename}} IS NOT NULL AND {{middlename}} != \'\'',
                $adapter->getConcatSql(['LTRIM(RTRIM({{middlename}}))', '\' \'']),
                '\'\'',
            );
        }
        $concatenate[] = 'LTRIM(RTRIM({{lastname}}))';
        if (isset($fields['suffix'])) {
            $concatenate[] = $adapter
                    ->getCheckSql(
                        '{{suffix}} IS NOT NULL AND {{suffix}} != \'\'',
                        $adapter->getConcatSql(['\' \'', 'LTRIM(RTRIM({{suffix}}))']),
                        '\'\'',
                    );
        }

        $nameExpr = $adapter->getConcatSql($concatenate);

        $this->addExpressionAttributeToSelect('name', $nameExpr, $fields);

        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->resetJoinLeft();

        return $select;
    }

    /**
     * Reset left join
     *
     * @param int $limit
     * @param int $offset
     * @return Varien_Db_Select
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = parent::_getAllIdsSelect($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $idsSelect;
    }
}
