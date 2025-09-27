<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("UPDATE {$this->getTable('core/url_rewrite')} SET type = IF(id_path LIKE 'category/%', 1, IF(id_path LIKE 'product/%', 2, 3));");

$installer->endSetup();
