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
 * @category    Mage
 * @package     Mage_Poll
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$conn->dropForeignKey('poll_answer', 'FK_POLL_PARENT');
$conn->dropForeignKey('poll_vote', 'FK_POLL_ANSWER');
$this->startSetup()
    ->run("
        ALTER TABLE {$this->getTable('poll')} CHANGE `poll_id` `poll_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
        ALTER TABLE {$this->getTable('poll_answer')} CHANGE `poll_id` `poll_id` INT UNSIGNED NOT NULL DEFAULT '0';
        ALTER TABLE {$this->getTable('poll_answer')} CHANGE `answer_id` `answer_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
        ALTER TABLE {$this->getTable('poll_vote')} CHANGE `poll_id` `poll_id` INT UNSIGNED NOT NULL DEFAULT '0';
        ALTER TABLE {$this->getTable('poll_vote')} CHANGE `poll_answer_id` `poll_answer_id` INT UNSIGNED NOT NULL DEFAULT '0';
        CREATE TABLE {$this->getTable('poll_store')} (
          `poll_id` int UNSIGNED NOT NULL,
          `store_id` smallint(5) unsigned NOT NULL,
          PRIMARY KEY  (`poll_id`,`store_id`),
          CONSTRAINT `FK_POLL_STORE` FOREIGN KEY (`poll_id`) REFERENCES {$this->getTable('poll')} (`poll_id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ALTER TABLE `{$this->getTable('poll_answer')}` ADD CONSTRAINT `FK_POLL_PARENT` FOREIGN KEY (`poll_id`) REFERENCES {$this->getTable('poll')} (`poll_id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ALTER TABLE `{$this->getTable('poll_vote')}` ADD CONSTRAINT `FK_POLL_ANSWER` FOREIGN KEY (`poll_answer_id`) REFERENCES {$this->getTable('poll_answer')} (`answer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
")
    ->endSetup();
