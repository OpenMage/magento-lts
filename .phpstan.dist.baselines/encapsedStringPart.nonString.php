<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Part $this->escapeHtml($this->getRole()->getRoleName()) (array<string|null>|string|null) of encapsed string cannot be cast to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Block/Adminhtml/Roles/Buttons.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Part $formModel (object|false) of encapsed string cannot be cast to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource/Validator/Eav.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Part $reg (array<string>|string) of encapsed string cannot be cast to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/AvailablePath.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Part $this->jsQuoteEscape($accountId) (array<string>|string) of encapsed string cannot be cast to string.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/GoogleAnalytics/Block/Ga.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Part $this->escapeHtml($this->getRoleInfo()->getRoleName()) (array<string|null>|string|null) of encapsed string cannot be cast to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/api/roleinfo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Part $this->escapeHtml($this->getRoleInfo()->getRoleName()) (array<string|null>|string|null) of encapsed string cannot be cast to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/permissions/roleinfo.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
