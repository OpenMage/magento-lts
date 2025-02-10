<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Tax_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('newsletter_queue_link');
$installer->getConnection()->addKey($table, 'IDX_NEWSLETTER_QUEUE_LINK_SEND_AT', ['queue_id', 'letter_sent_at']);

$installer->endSetup();
