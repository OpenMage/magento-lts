<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_Report_Product_Ordered:
after 1.4.0.1',
    'count' => 2,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_Report_Product_Ordered_Grid:
after 1.4.0.1',
    'count' => 2,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Giftmessage:
after 1.4.2.0 - gift column has been removed from search grid',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_System_Store_Grid:
after 1.13.1.0 use Mage_Adminhtml_Block_System_Store_Tree',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_System_Store_Grid_Render_Group:
after 1.13.1.0 use Mage_Adminhtml_Block_System_Store_Tree',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_System_Store_Grid_Render_Store:
after 1.13.1.0 use Mage_Adminhtml_Block_System_Store_Tree',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_System_Store_Grid_Render_Website:
after 1.13.1.0 use Mage_Adminhtml_Block_System_Store_Tree',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_Tag_Tag_Edit:
after 1.3.2.3',
    'count' => 2,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Adminhtml_Block_Tag_Tag_Edit_Form:
after 1.3.2.3',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Customer_Block_Account_Resetpassword.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_GiftMessage_Block_Message_Form:
after 1.3.2.4',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_GiftMessage_Block_Message_Helper:
after 1.3.2.4',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Page_Block_Html_Toplinks:
after 1.4.0.1',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Page_Block_Js_Translate:
since 1.7.0.0 (used in adminhtml/default/default/layout/main.xml)',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Global:
since 1.7.0.1',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Paypal_Block_Logo.',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Paypal_Block_Payflow_Advanced_Review:
since 1.6.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Paypal_Block_Payflow_Link_Review:
since 1.6.2.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_ProductAlert_Block_Price:
after 1.4.1.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_ProductAlert_Block_Stock:
after 1.4.1.0',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Access to constant on deprecated class Mage_Sales_Block_Order_Tax:
after 1.3.2.2',
    'count' => 1,
    'path' => __DIR__ . '/../tests/unit/Mage/Core/Model/LayoutTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
