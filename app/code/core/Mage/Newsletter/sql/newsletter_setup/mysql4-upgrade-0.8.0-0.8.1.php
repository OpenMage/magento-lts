<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */

/** @var Mage_Tax_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('newsletter_queue_link');
$installer->getConnection()->addKey($table, 'IDX_NEWSLETTER_QUEUE_LINK_SEND_AT', ['queue_id', 'letter_sent_at']);

$installer->endSetup();
