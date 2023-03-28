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
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        "rule_id=" . $r['rule_id']
    );
}
$conn->dropColumn($this->getTable('salesrule'), 'store_ids');

$installer->endSetup();
