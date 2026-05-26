<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var references deprecated class Mage_GiftMessage_Block_Message_Form:
after 1.3.2.4',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/giftmessage/form.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var references deprecated class Mage_GiftMessage_Block_Message_Helper:
after 1.3.2.4',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/giftmessage/helper.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var references deprecated class Mage_Page_Block_Js_Translate:
since 1.7.0.0 (used in adminhtml/default/default/layout/main.xml)',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/page/js/translate.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var references deprecated class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Global:
since 1.7.0.1',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/paypal/system/config/fieldset/global.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var references deprecated class Mage_Page_Block_Html_Toplinks:
after 1.4.0.1',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/page/html/top.links.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var references deprecated class Mage_Paypal_Block_Logo.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/paypal/partner/logo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var references deprecated class Mage_Paypal_Block_Logo.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/paypal/partner/us_logo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc tag @var references deprecated class Mage_Paypal_Block_Logo.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/paypal/partner/us_logo.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
