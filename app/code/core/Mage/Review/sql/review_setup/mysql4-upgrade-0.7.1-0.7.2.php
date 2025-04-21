<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Review/Rating module upgrade. Both modules tables must be installed.
 * @see app/etc/modules/Mage_All.xml - Review comes after Rating
 */

$this->startSetup();

$voteTable   = $this->getTable('rating_option_vote');
$reviewTable = $this->getTable('review');

$this->run("
DELETE FROM `{$voteTable}` WHERE `review_id` NOT IN (SELECT review_id FROM `{$reviewTable}`);
");

$this->run("
ALTER TABLE `{$voteTable}`
ADD CONSTRAINT `FK_RATING_OPTION_REVIEW_ID` FOREIGN KEY (`review_id`) REFERENCES `{$reviewTable}` (`review_id`)
ON DELETE CASCADE ON UPDATE CASCADE;
");

$this->endSetup();
