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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$codes = [
    'method' => [
        'EUROPEFIRSTINTERNATIONALPRIORITY'  => 'EUROPE_FIRST_INTERNATIONAL_PRIORITY',
        'FEDEX1DAYFREIGHT'                  => 'FEDEX_1_DAY_FREIGHT',
        'FEDEX2DAYFREIGHT'                  => 'FEDEX_2_DAY_FREIGHT',
        'FEDEX2DAY'                         => 'FEDEX_2_DAY',
        'FEDEX3DAYFREIGHT'                  => 'FEDEX_3_DAY_FREIGHT',
        'FEDEXEXPRESSSAVER'                 => 'FEDEX_EXPRESS_SAVER',
        'FEDEXGROUND'                       => 'FEDEX_GROUND',
        'FIRSTOVERNIGHT'                    => 'FIRST_OVERNIGHT',
        'GROUNDHOMEDELIVERY'                => 'GROUND_HOME_DELIVERY',
        'INTERNATIONALECONOMY'              => 'INTERNATIONAL_ECONOMY',
        'INTERNATIONALECONOMY FREIGHT'      => 'INTERNATIONAL_ECONOMY_FREIGHT',
        'INTERNATIONALFIRST'                => 'INTERNATIONAL_FIRST',
        'INTERNATIONALGROUND'               => 'INTERNATIONAL_GROUND',
        'INTERNATIONALPRIORITY'             => 'INTERNATIONAL_PRIORITY',
        'INTERNATIONALPRIORITY FREIGHT'     => 'INTERNATIONAL_PRIORITY_FREIGHT',
        'PRIORITYOVERNIGHT'                 => 'PRIORITY_OVERNIGHT',
        'SMARTPOST'                         => 'SMART_POST',
        'STANDARDOVERNIGHT'                 => 'STANDARD_OVERNIGHT',
        'FEDEXFREIGHT'                      => 'FEDEX_FREIGHT',
        'FEDEXNATIONALFREIGHT'              => 'FEDEX_NATIONAL_FREIGHT',
    ],
    'dropoff' => [
        'REGULARPICKUP'         => 'REGULAR_PICKUP',
        'REQUESTCOURIER'        => 'REQUEST_COURIER',
        'DROPBOX'               => 'DROP_BOX',
        'BUSINESSSERVICECENTER' => 'BUSINESS_SERVICE_CENTER',
        'STATION'               => 'STATION'
    ],
    'packaging' => [
        'FEDEXENVELOPE'     => 'FEDEX_ENVELOPE',
        'FEDEXPAK'          => 'FEDEX_PAK',
        'FEDEXBOX'          => 'FEDEX_BOX',
        'FEDEXTUBE'         => 'FEDEX_TUBE',
        'FEDEX10KGBOX'      => 'FEDEX_10KG_BOX',
        'FEDEX25KGBOX'      => 'FEDEX_25KG_BOX',
        'YOURPACKAGING'     => 'YOUR_PACKAGING'
    ],
];

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$configDataTable = $installer->getTable('core/config_data');
$conn = $installer->getConnection();

$select = $conn->select()
        ->from($configDataTable)
        ->where(
            'path IN (?)',
            [
                    'carriers/fedex/packaging',
                    'carriers/fedex/dropoff',
                    'carriers/fedex/free_method',
                    'carriers/fedex/allowed_methods'
                ]
        );
$mapsOld = $conn->fetchAll($select);
foreach ($mapsOld as $mapOld) {
    if (stripos($mapOld['path'], 'packaging') && isset($codes['packaging'][$mapOld['value']])) {
        $mapNew = $codes['packaging'][$mapOld['value']];
    } elseif (stripos($mapOld['path'], 'dropoff') && isset($codes['dropoff'][$mapOld['value']])) {
        $mapNew = $codes['dropoff'][$mapOld['value']];
    } elseif (stripos($mapOld['path'], 'free_method') && isset($codes['method'][$mapOld['value']])) {
        $mapNew = $codes['method'][$mapOld['value']];
    } elseif (stripos($mapOld['path'], 'allowed_methods')) {
        $mapNew = [];
        foreach (explode(',', $mapOld['value']) as $shippingMethod) {
            if (isset($codes['method'][$shippingMethod])) {
                $mapNew[] = $codes['method'][$shippingMethod];
            } else {
                $mapNew[] = $shippingMethod;
            }
        }
        $mapNew = implode(',', $mapNew);
    } else {
        continue;
    }

    if (!empty($mapNew) && $mapNew != $mapOld['value']) {
        $whereConfigId = $conn->quoteInto('config_id = ?', $mapOld['config_id']);
        $conn->update(
            $configDataTable,
            ['value' => $mapNew],
            $whereConfigId
        );
    }
}
