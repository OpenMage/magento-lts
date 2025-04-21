<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Contacts
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
UPDATE {$this->getTable('core_email_template')} SET `template_text` = 'Name: {{var data.name}}\r\nE-mail: {{var data.email}}\r\nTelephone: {{var data.telephone}}\r\n\r\nComment: {{var data.comment}}' WHERE template_code = 'Contact Form (Plain)';
");

$installer->endSetup();
