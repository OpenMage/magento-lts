<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Review
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
    $total = (int) $this->getConnection()->fetchOne($total);
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
