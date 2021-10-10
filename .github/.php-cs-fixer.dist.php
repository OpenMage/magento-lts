<?php

return (new PhpCsFixer\Config())
    ->setRules([
        'array_syntax' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->name('*.phtml')
        ->in('app')
        ->in('dev')
        ->in('errors')
        ->in('lib/Mage')
        ->in('lib/Magento')
        ->in('lib/Varien')
        ->in('lib/Zend')
        ->in('lib/Unserialize')
        ->in('shell')
    );
