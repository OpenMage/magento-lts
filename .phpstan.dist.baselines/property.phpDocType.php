<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc type Mage_Api_Model_Acl_Role_Registry of property Mage_Api_Model_Acl::$_roleRegistry is not the same as PHPDoc type Zend_Acl_Role_Registry of overridden property Zend_Acl::$_roleRegistry.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc type Mage_Core_Model_Config_Element of property Mage_Core_Model_Config::$_xml is not the same as PHPDoc type SimpleXMLElement of overridden property Varien_Simplexml_Config::$_xml.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc type Mage_Core_Model_App of property Mage_Tax_Block_Adminhtml_Notifications::$_app is not the same as PHPDoc type Mage_Core_Model_App|null of overridden property Mage_Core_Block_Abstract::$_app.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Block/Adminhtml/Notifications.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'PHPDoc type Mage_Weee_Helper_Data of property Mage_Weee_Model_Total_Quote_Weee::$_helper is not the same as PHPDoc type Mage_Tax_Helper_Data of overridden property Mage_Tax_Model_Sales_Total_Quote_Tax::$_helper.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Total/Quote/Weee.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
