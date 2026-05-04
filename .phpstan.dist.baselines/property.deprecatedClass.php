<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Property $_productCollection references deprecated class Mage_Wishlist_Model_Resource_Product_Collection in its type:
after 1.4.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property $subject references deprecated class Mage_Adminhtml_Helper_Media_Js in its type:
since 1.7.0.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Adminhtml/Helper/Media/JsTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property $subject references deprecated class Mage_Rule_Model_Rule in its type:
since 1.7.0.0 use Mage_Rule_Model_Abstract instead',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Rule/Model/RuleTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
