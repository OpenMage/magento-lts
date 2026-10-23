<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $_resPrices of class Mage_Catalog_Block_Product_View_Type_Configurable.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $_product of class Mage_Catalog_Model_Product_Type_Abstract:
if use as singleton',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $_editableAttributes of class Mage_Catalog_Model_Product_Type_Abstract.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $_allowTableChanges of class Mage_Catalog_Model_Resource_Category_Flat:
after 1.6.1.0',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $purifier of class Mage_Core_Helper_Purifier:
No longer used. See {@link static::$defaultPurifier}.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Purifier.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $_addresses of class Mage_Customer_Model_Customer:
after 1.4.0.0-rc1',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $_ratingOptionTable of class Mage_Rating_Model_Resource_Rating_Option_Collection:
since 1.5.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating/Option/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $_shipTable of class Mage_Shipping_Model_Resource_Carrier_Tablerate_Collection:
since 1.4.1.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Resource/Carrier/Tablerate/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to deprecated property $allowWhiteSpace of class Zend_Filter_Alnum.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Filter/FormElementName.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
