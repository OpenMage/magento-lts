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
UPDATE {$this->getTable('core_email_template')} SET `template_text` = 'Name: {{var data.name}}\r\nE-mail: {{var data.email}}\r\nTelephone: {{var data.telephone}}\r\n\r\nComment: {{var data.comment}}' WHERE template_code = 'Contact Form (Plain)';
");

$installer->endSetup();
