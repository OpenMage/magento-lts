<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->getConnection()->insert(
    $installer->getTable('core/config_data'),
    [
        'scope'    => 'default',
        'scope_id' => 0,
        'path'     => Mage_Directory_Helper_Data::XML_PATH_DISPLAY_ALL_STATES,
        'value'    => 1,
    ],
);

$countries = [];
foreach (Mage::helper('directory')->getCountryCollection() as $country) {
    if ($country->getRegionCollection()->getSize() > 0) {
        $countries[] = $country->getId();
    }
}

$installer->getConnection()->insert(
    $installer->getTable('core/config_data'),
    [
        'scope'    => 'default',
        'scope_id' => 0,
        'path'     => Mage_Directory_Helper_Data::XML_PATH_STATES_REQUIRED,
        'value'    => implode(',', $countries),
    ],
);
