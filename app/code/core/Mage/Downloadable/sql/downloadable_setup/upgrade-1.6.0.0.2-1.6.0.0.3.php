<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$connection->delete(
    $this->getTable('core_config_data'),
    $connection->prepareSqlCondition('path', [
        'like' => 'catalog/downloadable/content_disposition',
    ]),
);

$installer->endSetup();
