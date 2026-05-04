<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Instanceof between Mage_Sales_Model_Quote_Item_Option and Mage_Sales_Model_Quote_Item_Option will always evaluate to true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Item.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
