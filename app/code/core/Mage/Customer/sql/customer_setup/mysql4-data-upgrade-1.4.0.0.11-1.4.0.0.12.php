<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $this */
$installer = $this;

$attributes = [
    'prefix', 'firstname', 'middlename', 'lastname', 'suffix',
];

foreach ($attributes as $attributeCode) {
    $attribute   = Mage::getSingleton('eav/config')->getAttribute('customer_address', $attributeCode);
    $usedInForms = $attribute->getUsedInForms();
    if (!in_array('customer_register_address', $usedInForms)) {
        $usedInForms[] = 'customer_register_address';
        $attribute->setData('used_in_forms', $usedInForms);
        $attribute->save();
    }
}
