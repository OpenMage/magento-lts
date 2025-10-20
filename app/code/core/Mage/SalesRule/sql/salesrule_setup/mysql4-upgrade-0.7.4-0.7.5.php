<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$websites = $conn->fetchPairs("SELECT store_id, website_id FROM {$this->getTable('core_store')}");

$conn->addColumn($this->getTable('salesrule'), 'website_ids', 'text');

$select = $conn->select()
    ->from($this->getTable('salesrule'), ['rule_id', 'store_ids']);
$rows = $conn->fetchAll($select);

foreach ($rows as $r) {
    $websiteIds = [];
    foreach (explode(',', $r['store_ids']) as $storeId) {
        if ($storeId !== '') {
            $websiteIds[$websites[$storeId]] = true;
        }
    }

    $conn->update(
        $this->getTable('salesrule'),
        ['website_ids' => implode(',', array_keys($websiteIds))],
        'rule_id=' . $r['rule_id'],
    );
}

$conn->dropColumn($this->getTable('salesrule'), 'store_ids');

$installer->endSetup();
