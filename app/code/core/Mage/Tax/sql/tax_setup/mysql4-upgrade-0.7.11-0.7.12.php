<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('tax_calculation_rate')} CHANGE `zip_from` `zip_from` INT(11) UNSIGNED NULL DEFAULT NULL;
    ALTER TABLE {$this->getTable('tax_calculation_rate')} CHANGE `zip_to` `zip_to` INT(11) UNSIGNED NULL DEFAULT NULL;
");

$installer->endSetup();
