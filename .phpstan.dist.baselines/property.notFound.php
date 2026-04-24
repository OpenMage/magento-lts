<?php declare(strict_types = 1);

// total 16 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Zend_Session_Namespace::$data.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Varien_Object::$is_default_billing.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Varien_Object::$is_default_shipping.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Mage_Customer_Model_Customer::$_isSubscribed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Varien_Object::$domestic.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Varien_Object::$name.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Varien_Object::$region.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Mage_Core_Block_Template::$trackingInfo.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/sales/order/trackinginfo.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Varien_Data_Form_Element_Color::$original_data.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Color.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to an undefined property Throwable::$errorInfo.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
