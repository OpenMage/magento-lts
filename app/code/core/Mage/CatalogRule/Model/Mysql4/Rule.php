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
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog rules resource model
 */
class Mage_CatalogRule_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    const SECONDS_IN_DAY = 86400;

    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('catalogrule/rule', 'rule_id');
    }

    /**
     * Prepare object data for saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getFromDate()) {
            $date = new Zend_Date(Mage::getModel('core/date')->gmtTimestamp());
            $date->setHour(0)
                ->setMinute(0)
                ->setSecond(0);
            $object->setFromDate($date);
        }
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getToDate() instanceof Zend_Date) {
                $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
        parent::_beforeSave($object);
    }

    /**
     * Update products which are matched for rule
     *
     * @param   Mage_CatalogRule_Model_Rule $rule
     * @return  Mage_CatalogRule_Model_Mysql4_Rule
     */
    public function updateRuleProductData(Mage_CatalogRule_Model_Rule $rule)
    {
        $ruleId = $rule->getId();
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();

        $write->delete($this->getTable('catalogrule/rule_product'), $write->quoteInto('rule_id=?', $ruleId));

        if (!$rule->getIsActive()) {
            $write->commit();
            return $this;
        }

        $websiteIds = explode(',', $rule->getWebsiteIds());
        if (empty($websiteIds)) {
            return $this;
        }

        $productIds = $rule->getMatchingProductIds();
        $customerGroupIds = $rule->getCustomerGroupIds();

        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? ($toTime + self::SECONDS_IN_DAY - 1) : 0;

        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getDiscountAmount();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = array();
        $header = 'replace into '.$this->getTable('catalogrule/rule_product').' (
                rule_id,
                from_time,
                to_time,
                website_id,
                customer_group_id,
                product_id,
                action_operator,
                action_amount,
                action_stop,
                sort_order
            ) values ';

        try {
            foreach ($productIds as $productId) {
                foreach ($websiteIds as $websiteId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = "(
                            '$ruleId',
                            '$fromTime',
                            '$toTime',
                            '$websiteId',
                            '$customerGroupId',
                            '$productId',
                            '$actionOperator',
                            '$actionAmount',
                            '$actionStop',
                            '$sortOrder')";
                        if (sizeof($rows)==100) {
                            $sql = $header.join(',', $rows);
                            $write->query($sql);
                            $rows = array();
                        }
                    }
                }
            }
            if (!empty($rows)) {
                $sql = $header.join(',', $rows);
                $write->query($sql);
            }

            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * Get all product ids matched for rule
     *
     * @param   int $ruleId
     * @return  array
     */
    public function getRuleProductIds($ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('catalogrule/rule_product'), 'product_id')
            ->where('rule_id=?', $ruleId);
        return $read->fetchCol($select);
    }

    /**
     * Remove catalog rules product prices for specified date range and product
     *
     * @param   int|string $fromDate
     * @param   int|string $toDate
     * @param   int|null $productId
     * @return  Mage_CatalogRule_Model_Mysql4_Rule
     */
    public function removeCatalogPricesForDateRange($fromDate, $toDate, $productId=null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $cond = $write->quoteInto('rule_date between ?', $this->formatDate($fromDate));
        $cond = $write->quoteInto($cond.' and ?', $this->formatDate($toDate));
        $conds[] = $cond;
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id=?', $productId);
        }

        $write->delete($this->getTable('catalogrule/rule_product_price'), $conds);
        return $this;
    }

    /**
     * Delete old price rules data
     *
     * @param   int $maxDate
     * @param   mixed $productId
     * @return  Mage_CatalogRule_Model_Mysql4_Rule
     */
    public function deleteOldData($date, $productId=null)
    {
        $write = $this->_getWriteAdapter();
        $conds = array();
        $conds[] = $write->quoteInto('rule_date<?', $this->formatDate($date));
        if (!is_null($productId)) {
            $conds[] = $write->quoteInto('product_id=?', $productId);
        }
        $write->delete($this->getTable('catalogrule/rule_product_price'), $conds);
        return $this;
    }

    /**
     * Get rules data for all products in specified date range
     *
     * deprecated
     *
     * @param   int|string $fromDate
     * @param   int|string $toDate
     * @param   int|null $productId
     * @return  false|array
     */
    public function getRuleProductsForDateRange($fromDate, $toDate, $productId=null)
    {
        $read = $this->_getReadAdapter();

        $select = $read->select()
            ->from($this->getTable('catalogrule/rule_product'))
            ->where($read->quoteInto('from_time=0 or from_time<=?', strtotime($toDate))
            ." or ".$read->quoteInto('to_time=0 or to_time>=?', strtotime($fromDate)))
            ->order(array('website_id', 'customer_group_id', 'product_id', 'sort_order'));
        // crucial for logic sort order: website_id, customer_group_id, product_id, sort_order
        // had 'from_time', 'to_time' in the beginning before
        if (!is_null($productId)) {
            $select->where('product_id=?', $productId);
        }

        if (!$ruleProducts = $read->fetchAll($select)) {
            return false;
        }
        $productIds = array();
        foreach ($ruleProducts as $p) {
            $productIds[] = $p['product_id'];
        }

        $priceAttr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'price');

        $select = $read->select()
            ->from($priceAttr->getBackend()->getTable(), array('entity_id', 'store_id', 'value'))
            ->where('attribute_id=?', $priceAttr->getAttributeId())
            ->where('entity_id in (?)', $productIds)
            ->order('store_id');

        $prices = $read->fetchAll($select);

        /**
         * Prepare price information per website
         */
        $productPrices = array();
        foreach ($prices as $index => $priceData) {
            $websiteId = Mage::app()->getStore($priceData['store_id'])->getWebsiteId();

            if (!isset($productPrices[$priceData['entity_id']])) {
                $productPrices[$priceData['entity_id']] = array(
                    'default'    => $priceData['value'],
                    'websites'   => array($websiteId=>$priceData['value'])
                );
            }
            else {
                $productPrices[$priceData['entity_id']]['websites'][$websiteId] = $priceData['value'];
            }
        }

        foreach ($ruleProducts as &$p) {
            if (isset($productPrices[$p['product_id']]['websites'][$p['website_id']])) {
                $p['price'] = $productPrices[$p['product_id']]['websites'][$p['website_id']];
            }
            elseif (isset($productPrices[$p['product_id']]['default'])) {
                $p['price'] = $productPrices[$p['product_id']]['default'];
            }
        }

        return $ruleProducts;
    }

    /**
     * Get DB resource statment for processing query result
     *
     * @param   int $fromDate
     * @param   int $toDate
     * @param   int|null $productId
     * @param   int|null $websiteId
     * @return  Zend_Db_Statement_Interface
     */
    protected function _getRuleProductsStmt($fromDate, $toDate, $productId=null, $websiteId = null)
    {
        $read = $this->_getReadAdapter();
        /**
         * Sort order is important
         * It used for check stop price rule condition.
         * website_id   customer_group_id   product_id  sort_order
         *  1           1                   1           0
         *  1           1                   1           1
         *  1           1                   1           2
         * if row with sort order 1 will have stop flag we should exclude
         * all next rows for same product id from price calculation
         */
        $select = $read->select()
            ->from(array('rp'=>$this->getTable('catalogrule/rule_product')))
            ->where($read->quoteInto('rp.from_time=0 or rp.from_time<=?', $toDate)
            ." or ".$read->quoteInto('rp.to_time=0 or rp.to_time>=?', $fromDate))
            ->order(array('rp.website_id', 'rp.customer_group_id', 'rp.product_id', 'rp.sort_order'));

        if (!is_null($productId)) {
            $select->where('rp.product_id=?', $productId);
        }

        /**
         * Join default price and websites prices to result
         */
        $priceAttr  = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'price');
        $priceTable = $priceAttr->getBackend()->getTable();
        $attributeId= $priceAttr->getId();

        $joinCondition = '%1$s.entity_id=rp.product_id AND (%1$s.attribute_id='.$attributeId.') and %1$s.store_id=%2$s';

        $select->join(
            array('pp_default'=>$priceTable),
            sprintf($joinCondition, 'pp_default', Mage_Core_Model_App::ADMIN_STORE_ID),
            array('default_price'=>'pp_default.value')
        );

        if ($websiteId !== null) {
            $website  = Mage::app()->getWebsite($websiteId);
            $defaultGroup = $website->getDefaultGroup();
            if ($defaultGroup instanceof Mage_Core_Model_Store_Group) {
                $storeId    = $defaultGroup->getDefaultStoreId();
            } else {
                $storeId    = Mage_Core_Model_App::ADMIN_STORE_ID;
            }

            $select->joinInner(
                array('product_website'=>$this->getTable('catalog/product_website')),
                'product_website.product_id=rp.product_id AND product_website.website_id='.$websiteId,
                array()
            );

            $tableAlias = 'pp'.$websiteId;
            $fieldAlias = 'website_'.$websiteId.'_price';
            $select->joinLeft(
                array($tableAlias=>$priceTable),
                sprintf($joinCondition, $tableAlias, $storeId),
                array($fieldAlias=>$tableAlias.'.value')
            );
        } else {
	        foreach (Mage::app()->getWebsites() as $website) {
	            $websiteId  = $website->getId();
	            $defaultGroup = $website->getDefaultGroup();
	            if ($defaultGroup instanceof Mage_Core_Model_Store_Group) {
	                $storeId    = $defaultGroup->getDefaultStoreId();
	            } else {
                    $storeId    = Mage_Core_Model_App::ADMIN_STORE_ID;
                }

	            $storeId    = $defaultGroup->getDefaultStoreId();
	            $tableAlias = 'pp'.$websiteId;
	            $fieldAlias = 'website_'.$websiteId.'_price';
	            $select->joinLeft(
	                array($tableAlias=>$priceTable),
	                sprintf($joinCondition, $tableAlias, $storeId),
	                array($fieldAlias=>$tableAlias.'.value')
	            );
	        }
        }
        return $read->query($select);
    }

    /**
     * Generate catalog price rules prices for specified date range
     * If from date is not defined - will be used previous day by UTC
     * If to date is not defined - will be used next day by UTC
     *
     * @param   int|string|null $fromDate
     * @param   int|string|null $toDate
     * @param   int $productId
     * @return  Mage_CatalogRule_Model_Mysql4_Rule
     */
    public function applyAllRulesForDateRange($fromDate=null, $toDate=null, $productId=null)
    {
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();

        Mage::dispatchEvent('catalogrule_before_apply', array('resource'=>$this));

        $clearOldData = false;
        if ($fromDate === null) {
            $fromDate = mktime(0,0,0,date('m'),date('d')-1);
            /**
             * If fromDate not specified we can delete all data oldest than 1 day
             * We have run it for clear table in case when cron was not installed
             * and old data exist in table
             */
            $clearOldData = true;
        }
        if (is_string($fromDate)) {
            $fromDate = strtotime($fromDate);
        }
        if ($toDate === null) {
            $toDate = mktime(0,0,0,date('m'),date('d')+1);
        }
        if (is_string($toDate)) {
            $toDate = strtotime($toDate);
        }

        $product = null;
        if ($productId instanceof Mage_Catalog_Model_Product) {
            $product = $productId;
            $productId = $productId->getId();
        }

        $this->removeCatalogPricesForDateRange($fromDate, $toDate, $productId);
        if ($clearOldData) {
            $this->deleteOldData($fromDate, $productId);
        }

        try {
	        /**
	         * Update products rules prices per each website separatly
	         * because of max join limit in mysql
	         */
	        foreach (Mage::app()->getWebsites(false) as $website) {
	            $productsStmt = $this->_getRuleProductsStmt(
	               $fromDate,
	               $toDate,
	               $productId,
	               $website->getId()
	            );

	            $dayPrices  = array();
	            $stopFlags  = array();
	            $prevKey    = null;

	            while ($ruleData = $productsStmt->fetch()) {
	                $productId = $ruleData['product_id'];
	                $productKey= $productId . '_'
	                   . $ruleData['website_id'] . '_'
	                   . $ruleData['customer_group_id'];

	                if ($prevKey && ($prevKey != $productKey)) {
	                    $stopFlags = array();
	                }

	                /**
	                 * Build prices for each day
	                 */
	                for ($time=$fromDate; $time<=$toDate; $time+=self::SECONDS_IN_DAY) {
	                    if (($ruleData['from_time']==0 || $time >= $ruleData['from_time'])
	                        && ($ruleData['to_time']==0 || $time <=$ruleData['to_time'])) {

	                        $priceKey = $time . '_' . $productKey;

	                        if (isset($stopFlags[$priceKey])) {
	                            continue;
	                        }

	                        if (!isset($dayPrices[$priceKey])) {
	                            $dayPrices[$priceKey] = array(
	                                'rule_date'         => $time,
	                                'website_id'        => $ruleData['website_id'],
	                                'customer_group_id' => $ruleData['customer_group_id'],
	                                'product_id'        => $productId,
	                                'rule_price'        => $this->_calcRuleProductPrice($ruleData),
	                                'latest_start_date' => $ruleData['from_time'],
	                                'earliest_end_date' => $ruleData['to_time'],
	                            );
	                        }
	                        else {
	                            $dayPrices[$priceKey]['rule_price'] = $this->_calcRuleProductPrice(
	                                $ruleData,
	                                $dayPrices[$priceKey]
	                            );
	                            $dayPrices[$priceKey]['latest_start_date'] = max(
	                                $dayPrices[$priceKey]['latest_start_date'],
	                                $ruleData['from_time']
	                            );
	                            $dayPrices[$priceKey]['earliest_end_date'] = min(
	                                $dayPrices[$priceKey]['earliest_end_date'],
	                                $ruleData['to_time']
	                            );
	                        }

	                        if ($ruleData['action_stop']) {
	                            $stopFlags[$priceKey] = true;
	                        }
	                    }
	                }

	                $prevKey = $productKey;

	                if (count($dayPrices)>100) {
	                    $this->_saveRuleProductPrices($dayPrices);
	                    $dayPrices = array();
	                }
	            }
	            $this->_saveRuleProductPrices($dayPrices);
	        }
	        $this->_saveRuleProductPrices($dayPrices);
	        $write->commit();

	        //
//            $dayPrices  = array();
//            $stopFlags  = array();
//            $prevKey    = null;
//            while ($ruleData = $productsStmt->fetch()) {
//                $productId = $ruleData['product_id'];
//                $productKey= $productId . '_' . $ruleData['website_id'] . '_' . $ruleData['customer_group_id'];
//
//                if ($prevKey && ($prevKey != $productKey)) {
//                    $stopFlags = array();
//                }
//
//                /**
//                 * Build prices for each day
//                 */
//                for ($time=$fromDate; $time<=$toDate; $time+=self::SECONDS_IN_DAY) {
//
//                    if (($ruleData['from_time']==0 || $time >= $ruleData['from_time'])
//                        && ($ruleData['to_time']==0 || $time <=$ruleData['to_time'])) {
//
//                        $priceKey = $time . '_' . $productKey;
//
//                        if (isset($stopFlags[$priceKey])) {
//                            continue;
//                        }
//
//                        if (!isset($dayPrices[$priceKey])) {
//                            $dayPrices[$priceKey] = array(
//                                'rule_date'         => $time,
//                                'website_id'        => $ruleData['website_id'],
//                                'customer_group_id' => $ruleData['customer_group_id'],
//                                'product_id'        => $productId,
//                                'rule_price'        => $this->_calcRuleProductPrice($ruleData),
//                                'latest_start_date' => $ruleData['from_time'],
//                                'earliest_end_date' => $ruleData['to_time'],
//                            );
//                        }
//                        else {
//                            $dayPrices[$priceKey]['rule_price'] = $this->_calcRuleProductPrice(
//                                $ruleData,
//                                $dayPrices[$priceKey]
//                            );
//                            $dayPrices[$priceKey]['latest_start_date'] = max(
//                                $dayPrices[$priceKey]['latest_start_date'],
//                                $ruleData['from_time']
//                            );
//                            $dayPrices[$priceKey]['earliest_end_date'] = min(
//                                $dayPrices[$priceKey]['earliest_end_date'],
//                                $ruleData['to_time']
//                            );
//                        }
//
//                        if ($ruleData['action_stop']) {
//                            $stopFlags[$priceKey] = true;
//                        }
//                    }
//                }
//
//                $prevKey = $productKey;
//
//                if (count($dayPrices)>100) {
//                    $this->_saveRuleProductPrices($dayPrices);
//                    $dayPrices = array();
//                }
//            }
//            $this->_saveRuleProductPrices($dayPrices);
//            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }

        $productCondition = Mage::getModel('catalog/product_condition')
            ->setTable($this->getTable('catalogrule/affected_product'))
            ->setPkFieldName('product_id');
        Mage::dispatchEvent('catalogrule_after_apply', array(
            'product'=>$product,
            'product_condition' => $productCondition
        ));
        $write->delete($this->getTable('catalogrule/affected_product'));

        return $this;
    }

    /**
     * Calculate product price based on price rule data and previous information
     *
     * @param   array $ruleData
     * @param   null|array $productData
     * @return  float
     */
    protected function _calcRuleProductPrice($ruleData, $productData=null)
    {
        if ($productData !== null && isset($productData['rule_price'])) {
            $productPrice = $productData['rule_price'];
        }
        else {
            $websiteId = $ruleData['website_id'];
            if (isset($ruleData['website_'.$websiteId.'_price'])) {
                $productPrice = $ruleData['website_'.$websiteId.'_price'];
            }
            else {
                $productPrice = $ruleData['default_price'];
            }
        }

        $amount = $ruleData['action_amount'];
        switch ($ruleData['action_operator']) {
            case 'to_fixed':
                $productPrice = $amount;
                break;
            case 'to_percent':
                $productPrice= $productPrice*$amount/100;
                break;
            case 'by_fixed':
                $productPrice -= $amount;
                break;
            case 'by_percent':
                $productPrice = $productPrice*(1-$amount/100);
                break;
        }

        $productPrice = max($productPrice, 0);
        return Mage::app()->getStore()->roundPrice($productPrice);
    }

    /**
     * Save rule prices for products to DB
     *
     * @param   array $arrData
     * @return  Mage_CatalogRule_Model_Mysql4_Rule
     */
    protected function _saveRuleProductPrices($arrData)
    {
        if (empty($arrData)) {
            return $this;
        }
        $header = 'replace into '.$this->getTable('catalogrule/rule_product_price').' (
                rule_date,
                website_id,
                customer_group_id,
                product_id,
                rule_price,
                latest_start_date,
                earliest_end_date
            ) values ';
        $rows = array();
        $productIds = array();
        foreach ($arrData as $data) {
            $productIds[$data['product_id']] = true;
            $data['rule_date']          = $this->formatDate($data['rule_date'], false);
            $data['latest_start_date']  = $this->formatDate($data['latest_start_date'], false);
            $data['earliest_end_date']  = $this->formatDate($data['earliest_end_date'], false);
            $rows[] = '(' . $this->_getWriteAdapter()->quote($data) . ')';
        }
        $query = $header.join(',', $rows);
        $insertQuery = 'REPLACE INTO ' . $this->getTable('catalogrule/affected_product') . ' (product_id)  VALUES ' .
            '(' . join('),(', array_keys($productIds)) . ')';
        $this->_getWriteAdapter()->query($insertQuery);
        $this->_getWriteAdapter()->query($query);
        return $this;
    }

    /**
     * Get catalog rules product price for specific date, website and
     * customer group
     *
     * @param   int|string $date
     * @param   int $wId
     * @param   int $gId
     * @param   int $pId
     * @return  float | false
     */
    public function getRulePrice($date, $wId, $gId, $pId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('catalogrule/rule_product_price'), 'rule_price')
            ->where('rule_date=?', $this->formatDate($date, false))
            ->where('website_id=?', $wId)
            ->where('customer_group_id=?', $gId)
            ->where('product_id=?', $pId);
        return $read->fetchOne($select);
    }

    /**
     * Get data about product prices for all customer groups
     *
     * @param   int|string $date
     * @param   int $wId
     * @param   int $pId
     * @return  array
     */
    public function getRulesForProduct($date, $wId, $pId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('catalogrule/rule_product_price'), '*')
            ->where('rule_date=?', $this->formatDate($date, false))
            ->where('website_id=?', $wId)
            ->where('product_id=?', $pId);
        return $read->fetchAll($select);
    }

    /**
     * Apply catalog rule to product
     *
     * @param   Mage_CatalogRule_Model_Rule $rule
     * @param   Mage_Catalog_Model_Product $product
     * @param   array $websiteIds
     * @return  Mage_CatalogRule_Model_Mysql4_Rule
     */
    public function applyToProduct($rule, $product, $websiteIds)
    {
        if (!$rule->getIsActive()) {
            return $this;
        }

        $ruleId = $rule->getId();
        $productId = $product->getId();

        $write = $this->_getWriteAdapter();
        $write->beginTransaction();

        $write->delete($this->getTable('catalogrule/rule_product'), array(
            $write->quoteInto('rule_id=?', $ruleId),
            $write->quoteInto('product_id=?', $productId),
        ));

        if (!$rule->getConditions()->validate($product)) {
            $write->delete($this->getTable('catalogrule/rule_product_price'), array(
                $write->quoteInto('product_id=?', $productId),
            ));
            $write->commit();
            return $this;
        }

        $customerGroupIds = $rule->getCustomerGroupIds();

        $fromTime   = strtotime($rule->getFromDate());
        $toTime     = strtotime($rule->getToDate());
        $toTime     = $toTime ? $toTime+self::SECONDS_IN_DAY-1 : 0;

        $sortOrder      = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount   = $rule->getDiscountAmount();
        $actionStop     = $rule->getStopRulesProcessing();

        $rows = array();
        $header = 'replace into '.$this->getTable('catalogrule/rule_product').' (
                rule_id,
                from_time,
                to_time,
                website_id,
                customer_group_id,
                product_id,
                action_operator,
                action_amount,
                action_stop,
                sort_order
            ) values ';
        try {
            foreach ($websiteIds as $websiteId) {
                foreach ($customerGroupIds as $customerGroupId) {
                    $rows[] = "(
                        '$ruleId',
                        '$fromTime',
                        '$toTime',
                        '$websiteId',
                        '$customerGroupId',
                        '$productId',
                        '$actionOperator',
                        '$actionAmount',
                        '$actionStop',
                        '$sortOrder'
                    )";
                    if (sizeof($rows)==100) {
                        $sql = $header.join(',', $rows);
                        $write->query($sql);
                        $rows = array();
                    }
                }
            }

            if (!empty($rows)) {
                $sql = $header.join(',', $rows);
                $write->query($sql);
            }
        } catch (Exception $e) {
            $write->rollback();
            throw $e;

        }
        $this->applyAllRulesForDateRange(null, null, $product);
        $write->commit();
        return $this;
    }
}