<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Contacts
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
INSERT INTO {$this->getTable('core_email_template')} (`template_code`, `template_text`, `template_type`, `template_subject`, `template_sender_name`, `template_sender_email`, `added_at`, `modified_at`) VALUES
('Contact Form (Plain)', 'Name: {{var data.name}}\r\nTitle: {{var data.title}}\r\nE-mail: {{var data.email}}\r\nTelephone: {{var data.telephone}}\r\nCompany: {{var data.company}}\r\nWebsite URL: {{var data.website}}\r\n\r\nComment: {{var data.comment}}', 1, 'Contact Form', NULL, NULL, NOW(), NOW());
");
$installer->endSetup();
