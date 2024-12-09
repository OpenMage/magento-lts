<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

// update UPS Gateway XML URL to the new recommended one
$installer->run("UPDATE {$installer->getTable('core/config_data')} SET `value` = REPLACE(`value`,
        'https://www.ups.com/ups.app/xml/Rate', 'https://onlinetools.ups.com/ups.app/xml/Rate'
    ) WHERE `path` = 'carriers/ups/gateway_xml_url'");
