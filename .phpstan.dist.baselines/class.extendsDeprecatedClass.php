<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Class Mage_Reports_Model_Resource_Wishlist_Product_Collection extends deprecated class Mage_Wishlist_Model_Resource_Product_Collection:
after 1.4.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Wishlist/Product/Collection.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
