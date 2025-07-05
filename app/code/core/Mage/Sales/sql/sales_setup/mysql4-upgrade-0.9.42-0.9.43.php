<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
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
