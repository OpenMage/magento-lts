<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Unable to resolve the template type T in call to method Mage_Core_Block_Abstract::escapeHtml()',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/shipping/tracking/popup.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
