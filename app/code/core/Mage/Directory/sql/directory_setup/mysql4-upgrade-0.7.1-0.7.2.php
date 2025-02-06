<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup()
    ->run("
INSERT INTO {$this->getTable('core_email_template')} (`template_code`, `template_text`, `template_type`,
`template_subject`, `template_sender_name`, `template_sender_email`, `added_at`, `modified_at`) VALUES
('Currency Update Warnings', 'Currency update warnings:\r\n\r\n\r\n{{var warnings}}', 1,
'Currency Update Warnings', NULL, NULL, '2008-01-30 14:10:31', '2008-01-30 16:02:47');

");

$installer->endSetup();
