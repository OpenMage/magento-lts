<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
