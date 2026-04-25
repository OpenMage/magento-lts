<?php declare(strict_types = 1);

// total 2 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 (non-empty-array) of echo cannot be converted to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/review/helper/summary.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Parameter #1 (non-empty-array) of echo cannot be converted to string.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/review/helper/summary_short.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
