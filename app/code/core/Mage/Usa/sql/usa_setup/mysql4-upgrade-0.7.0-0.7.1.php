<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Usa
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

// update UPS Gateway XML URL to the new recommended one
$installer->run("UPDATE {$installer->getTable('core/config_data')} SET `value` = REPLACE(`value`,
        'https://www.ups.com/ups.app/xml/Rate', 'https://onlinetools.ups.com/ups.app/xml/Rate'
    ) WHERE `path` = 'carriers/ups/gateway_xml_url'");
