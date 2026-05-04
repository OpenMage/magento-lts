<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Class Mage_Captcha_Model_Zend extends @final class Laminas\\Captcha\\Image.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Zend.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
