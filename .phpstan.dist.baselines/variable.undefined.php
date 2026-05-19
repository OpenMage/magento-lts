<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $billingAddress might not be defined.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $adapters might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $operationName might not be defined.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $defaultValues might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $productId might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $store might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Widget/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $condition might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $options might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $websiteId might not be defined.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $idField might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $whereField might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $customer might not be defined.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Customer/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $key might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Template/Facade.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $str might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $regRule might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $alias might not be defined.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $regionId might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Undefined variable: $action',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Undefined variable: $profile',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Profile/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $fields might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Session/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $defBind might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Entity.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $entityIds might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Entity.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $entity might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $result might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Rate/Result.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $tmp might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Rate/Result.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $result might not be defined.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $wishlist might not be defined.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Variable $out might not be defined.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/DB/Mysqli.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
