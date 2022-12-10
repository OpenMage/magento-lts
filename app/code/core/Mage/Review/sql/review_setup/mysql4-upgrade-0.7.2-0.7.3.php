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
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review/Rating module upgrade. Both modules tables must be installed.
 * @see app/etc/modules/Mage_All.xml - Review comes after Rating
 */

$this->startSetup();

// add average approved percent
$this->run("
ALTER TABLE `{$this->getTable('rating_option_vote_aggregated')}`
ADD COLUMN `percent_approved` tinyint(3) NULL DEFAULT 0 AFTER `percent`;
");

try {
    // re-aggregate existing reviews
    $resource = Mage::getResourceSingleton('review/review');
    // count quantity and aggregate packs per 100 items
    $total = $this->getConnection()->select()->from($this->getTable('review'), 'count(*)');
    $total = intval($this->getConnection()->fetchOne($total));
    for ($i = 0; $i < $total; $i += 100) {
        $select = $this->getConnection()->select()
            ->from($this->getTable('review'), ['review_id', 'entity_pk_value'])
            ->limit(100, $i)
        ;
        $rows = $this->getConnection()->fetchAll($select);
        foreach ($rows as $row) {
            $resource->reAggregateReview($row['review_id'], $row['entity_pk_value']);
        }
    }
} catch (Exception $e) {
    $this->run("ALTER TABLE `{$this->getTable('rating_option_vote_aggregated')}` DROP COLUMN `percent_approved`;");
    throw $e;
}

$this->endSetup();
