<?php

/**
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->startSetup();

$orderAddressEntityTypeId = 'order_address';
$attributeLabels = [
    'firstname' => 'First Name',
    'lastname' => 'Last Name',
    'company' => 'Company',
    'street' => 'Street Address',
    'city' => 'City',
    'region_id' => 'State/Province',
    'postcode' => 'Zip/Postal Code',
    'country_id' => 'Country',
    'telephone' => 'Telephone',
    'email' => 'Email',
];

foreach ($attributeLabels as $attributeCode => $attributeLabel) {
    $this->updateAttribute($orderAddressEntityTypeId, $attributeCode, 'frontend_label', $attributeLabel);
}

$this->endSetup();
