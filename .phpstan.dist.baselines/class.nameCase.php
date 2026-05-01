<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Class Mage_Paypal_Block_Express_Form referenced with incorrect case: Mage_PayPal_Block_Express_Form.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/paypal/payment/redirect.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
