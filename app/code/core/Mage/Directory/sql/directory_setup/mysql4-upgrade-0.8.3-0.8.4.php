<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
INSERT INTO `{$installer->getTable('directory_country')}` (`country_id`, `iso2_code`, `iso3_code`) VALUES
('GG', 'GG', 'GGY'),('IM', 'IM', 'IMN'),('JE', 'JE', 'JEY'),('ME', 'ME', 'MNE'),
('BL', 'BL', 'BLM'),('MF', 'MF', 'MAF'),('RS', 'RS', 'SRB'),('TL', 'TL', 'TLS');
");

$installer->endSetup();
