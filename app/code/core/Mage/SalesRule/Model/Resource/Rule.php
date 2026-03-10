<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * Sales Rule resource model
 *
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Rule extends Mage_Rule_Model_Resource_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'website' => [
            'associations_table' => 'salesrule/website',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'website_id',
        ],
        'customer_group' => [
            'associations_table' => 'salesrule/customer_group',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'customer_group_id',
        ],
    ];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('salesrule/rule', 'rule_id');
    }

    /**
     * Add customer group ids and website ids to rule data after load
     *
     * @return $this
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->setData('customer_group_ids', (array) $this->getCustomerGroupIds($object->getId()));
        $object->setData('website_ids', (array) $this->getWebsiteIds($object->getId()));

        parent::_afterLoad($object);
        return $this;
    }

    /**
     * Prepare sales rule's discount quantity
     *
     * @return $this
     * @throws Zend_Date_Exception
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getDiscountQty()) {
            $object->setDiscountQty(new Zend_Db_Expr('NULL'));
        }

        $dateFrom = $object->getFromDate();
        $dateTo = $object->getToDate();

        # fix when from and to day are the same
        if (($dateFrom instanceof Zend_Date && $dateTo instanceof Zend_Date)
            && ($dateFrom->getTimestamp() === $dateTo->getTimestamp())
        ) {
            $dateTo->setHour(23)->setMinute(59)->setSecond(59);
        }

        parent::_beforeSave($object);
        return $this;
    }

    /**
     * Bind sales rule to customer group(s) and website(s).
     * Save rule's associated store labels.
     * Save product attributes used in rule.
     *
     * @param Mage_SalesRule_Model_Rule $object
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        if ($object->hasWebsiteIds()) {
            $websiteIds = $object->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', (string) $websiteIds);
            }

            $this->bindRuleToEntity($object->getId(), $websiteIds, 'website');
        }

        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string) $customerGroupIds);
            }

            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        // Save product attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->getProductAttributes(serialize($object->getConditions()->asArray())),
            $this->getProductAttributes(serialize($object->getActions()->asArray())),
        );
        if ($ruleProductAttributes !== []) {
            $this->setActualProductAttributes($object, $ruleProductAttributes);
        }

        // Update auto geterated specific coupons if exists
        if ($object->getUseAutoGeneration() && $object->hasDataChanges()) {
            Mage::getResourceModel('salesrule/coupon')->updateSpecificCoupons($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve coupon/rule uses for specified customer
     *
     * @param Mage_SalesRule_Model_Rule $rule
     * @param int                       $customerId
     *
     * @return string
     */
    public function getCustomerUses($rule, $customerId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('rule_customer'), ['cnt' => 'count(*)'])
            ->where('rule_id = :rule_id')
            ->where('customer_id = :customer_id');
        return $read->fetchOne($select, [':rule_id' => $rule->getRuleId(), ':customer_id' => $customerId]);
    }

    /**
     * Save rule labels for different store views
     *
     * @param int   $ruleId
     * @param array $labels
     *
     * @return $this
     */
    public function saveStoreLabels($ruleId, $labels)
    {
        $deleteByStoreIds = [];
        $table   = $this->getTable('salesrule/label');
        $adapter = $this->_getWriteAdapter();

        $data    = [];
        foreach ($labels as $storeId => $label) {
            if (Mage::helper('core/string')->strlen($label)) {
                $data[] = ['rule_id' => $ruleId, 'store_id' => $storeId, 'label' => $label];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $adapter->beginTransaction();
        try {
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $table,
                    $data,
                    ['label'],
                );
            }

            if (!empty($deleteByStoreIds)) {
                $adapter->delete($table, [
                    'rule_id=?'       => $ruleId,
                    'store_id IN (?)' => $deleteByStoreIds,
                ]);
            }

            $adapter->commit();
        } catch (Exception $exception) {
            $adapter->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Get all existing rule labels
     *
     * @param  int   $ruleId
     * @return array
     */
    public function getStoreLabels($ruleId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('salesrule/label'), ['store_id', 'label'])
            ->where('rule_id = :rule_id');
        return $this->_getReadAdapter()->fetchPairs($select, [':rule_id' => $ruleId]);
    }

    /**
     * Get rule label by specific store id
     *
     * @param  int    $ruleId
     * @param  int    $storeId
     * @return string
     */
    public function getStoreLabel($ruleId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('salesrule/label'), 'label')
            ->where('rule_id = :rule_id')
            ->where('store_id IN(0, :store_id)')
            ->order('store_id DESC');
        return $this->_getReadAdapter()->fetchOne($select, [':rule_id' => $ruleId, ':store_id' => $storeId]);
    }

    /**
     * Return codes of all product attributes currently used in promo rules for specified customer group and website
     *
     * @param  int   $websiteId
     * @param  int   $customerGroupId
     * @return mixed
     */
    public function getActiveAttributes($websiteId, $customerGroupId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(
                ['a' => $this->getTable('salesrule/product_attribute')],
                new Zend_Db_Expr('DISTINCT ea.attribute_code'),
            )
            ->joinInner(['ea' => $this->getTable('eav/attribute')], 'ea.attribute_id = a.attribute_id', []);
        return $read->fetchAll($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of rule
     *
     * @param  Mage_SalesRule_Model_Rule $rule
     * @param  mixed                     $attributes
     * @return $this
     */
    public function setActualProductAttributes($rule, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('salesrule/product_attribute'), ['rule_id=?' => $rule->getId()]);

        //Getting attribute IDs for attribute codes
        $attributeIds = [];
        $select = $this->_getReadAdapter()->select()
            ->from(['a' => $this->getTable('eav/attribute')], ['a.attribute_id'])
            ->where('a.attribute_code IN (?)', [$attributes]);
        $attributesFound = $this->_getReadAdapter()->fetchAll($select);
        if ($attributesFound) {
            foreach ($attributesFound as $attribute) {
                $attributeIds[] = $attribute['attribute_id'];
            }

            $data = [];
            foreach ($rule->getCustomerGroupIds() as $customerGroupId) {
                foreach ($rule->getWebsiteIds() as $websiteId) {
                    foreach ($attributeIds as $attribute) {
                        $data[] = [
                            'rule_id'           => $rule->getId(),
                            'website_id'        => $websiteId,
                            'customer_group_id' => $customerGroupId,
                            'attribute_id'      => $attribute,
                        ];
                    }
                }
            }

            $write->insertMultiple($this->getTable('salesrule/product_attribute'), $data);
        }

        return $this;
    }

    /**
     * Collect all product attributes used in serialized rule's action or condition
     *
     * @param string $serializedString
     *
     * @return array
     */
    public function getProductAttributes($serializedString)
    {
        $result = [];
        if (preg_match_all('~s:32:"salesrule/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s', $serializedString, $matches)) {
            foreach ($matches[1] as $attributeCode) {
                $result[] = $attributeCode;
            }
        }

        return $result;
    }
}
