<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$this->startSetup();

$this->getConnection()->changeColumn(
    $this->getTable('api/user'),
    'api_key',
    'api_key',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Api key'
    ]
);

$this->endSetup();
