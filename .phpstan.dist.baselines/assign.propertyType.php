<?php declare(strict_types = 1);

// total 9 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Property Mage_Adminhtml_Model_Config::$_config (Mage_Core_Model_Config_Base) does not accept Varien_Simplexml_Config.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property Mage_Catalog_Model_Convert_Adapter_Product::$_galleryBackendModel (Mage_Catalog_Model_Product_Attribute_Backend_Media) does not accept Mage_Eav_Model_Entity_Attribute_Backend_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property Mage_Core_Controller_Varien_Action::$_request (Mage_Core_Controller_Request_Http) does not accept Zend_Controller_Request_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property Mage_Core_Controller_Varien_Action::$_response (Mage_Core_Controller_Response_Http) does not accept Zend_Controller_Response_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property Mage_Core_Model_Resource_Setup::$_resourceConfig (Mage_Core_Model_Config_Element) does not accept SimpleXMLElement.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property Mage_Dataflow_Model_Convert_Action_Abstract::$_container (Mage_Dataflow_Model_Convert_Container_Abstract) does not accept Mage_Dataflow_Model_Convert_Container_Interface.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Action/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property Mage_Eav_Model_Attribute_Data_Abstract::$_attribite (Mage_Eav_Model_Attribute) does not accept Mage_Eav_Model_Entity_Attribute_Abstract.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property Mage_Eav_Model_Entity_Attribute_Abstract::$_entity (Mage_Eav_Model_Entity_Abstract) does not accept Mage_Eav_Model_Entity_Type.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Property DOMNode::$nodeValue (string|null) does not accept int<min, -1>|int<1, max>.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Test.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
