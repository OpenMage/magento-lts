<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

// update UPS Gateway XML URL to the new recommended one
$installer->run("UPDATE {$installer->getTable('core/config_data')} SET `value` = REPLACE(`value`,
        'https://www.ups.com/ups.app/xml/Rate', 'https://onlinetools.ups.com/ups.app/xml/Rate'
    ) WHERE `path` = 'carriers/ups/gateway_xml_url'");
