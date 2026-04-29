<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Comparison operation ">" between int<1, max> and 0 is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/grid.phtml',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
