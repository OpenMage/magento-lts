<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideApiTimeoutData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/ConfigTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideGetRetryConfigurationData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/ConfigTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideInvalidRetryHttpMethodsData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/System/Config/Backend/RetryHttpMethodsTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideValidRetryHttpMethodsData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/System/Config/Backend/RetryHttpMethodsTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideInvalidRetryStatusCodesData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/System/Config/Backend/RetryStatusCodesTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideValidRetryStatusCodesData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/System/Config/Backend/RetryStatusCodesTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideOptionValuesData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/System/Config/Source/RetryHttpMethodsTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideCandidateIdsData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/Webhook/Event/ResolverTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideIncrementIdsData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/Webhook/Event/ResolverTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideEventActionData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/Webhook/ProcessorTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideRequiredHeadersData related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/Webhook/VerifierTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => '@@dataProvider provideVerificationResponses related method must be static in PHPUnit 10 and newer.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Paypal/Model/Webhook/VerifierTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
