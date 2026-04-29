<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/Mage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/Rules.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Mage_Core_Model_Security_Obfuscated and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Varien_Simplexml_Element|null and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Admin/Model/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Api/Buttons.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Api/Tab/Rolesedit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Helper/Pricestep.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Helper/Sortby/Available.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Helper/Sortby/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tab/Attributes.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tab/Attributes.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tab/Attributes.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Category/Widget/Chooser.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and array|false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Form/Renderer/Fieldset/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Form/Renderer/Fieldset/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Form/Renderer/Fieldset/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Attribute/Edit/Tab/Main.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Attribute/New/Product/Created.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Attribute/Set/Main.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Attribute/Set/Main.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Composite/Fieldset.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Categories.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Inventory.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Options/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Options/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Options/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Price/Group/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array and array is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Super/Config/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Super/Config/Simple.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Apply.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and array|false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Gallery.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Gallery.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Edit/Tab/Newsletter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Grid/Renderer/Multiaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Grid/Renderer/Multiaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Customer/Group/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Queue/Grid/Renderer/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Subscriber/Grid/Renderer/Checkbox.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Grid/Renderer/Sender.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Notification/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Notification/Security.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Notification/Toolbar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Page/Menu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Page/Menu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Buttons.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/Role/Grid/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Permissions/User/Edit/Tab/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Promo/Quote/Edit/Tab/Labels.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Promo/Quote/Edit/Tab/Main.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and null is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Report/Grid/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Grid/Filter/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Grid/Renderer/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Review/Rating/Detailed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Items/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Items/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Billing/Method/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Form/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Form/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Items/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Search/Grid/Renderer/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Search/Grid/Renderer/Qty.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Create/Shipping/Method/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/Status/New/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Tab/History.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Form/Field.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Switcher.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/System/Storage/Media/Synchronize.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Config/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and bool|int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Gui/Edit/Tab/Wizard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Gui/Edit/Tab/Wizard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Profile/Edit/Filter/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Profile/Edit/Tab/Run.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Convert/Profile/Edit/Tab/Run.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Email/Template/Grid/Renderer/Sender.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<min, -1>|int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<min, -1>|int<1, max>|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 11,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/System/Store/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tag/Tag/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tax/Class.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tax/Class/Edit/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tax/Rate/Grid/Renderer/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Tax/Rate/Toolbar/Save.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Theme.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Theme.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Theme.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Theme.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Block/Widget/Tabs.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Controller/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Controller/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Helper/Rss.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Customer/Renderer/Region.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/Sales/Order/Create.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Admin/Usecustom.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Currency/Cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Customer/Address/Street.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Customer/Show/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Filename.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Log/Cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Product/Alert/Cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Sitemap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Sitemap/Cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/Model/System/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/Action/AttributeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/ReviewController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/Product/SetController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Catalog/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/CustomerController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Newsletter/QueueController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Permissions/UserController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Promo/QuoteController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Sales/Order/View/GiftmessageController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/CacheController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/CacheController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/Config/System/StorageController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/ConfigController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/ConfigController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/CurrencyController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/System/StoreController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Tax/RateController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and array<int, string> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Tax/RateController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Tax/RateController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Adminhtml/controllers/Tax/RateController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Acl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Rules.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/Rules.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Resource/User.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Server/Handler/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Api/Model/Wsdl/Config/Element.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Block/Adminhtml/Permissions/User/Edit/Tab/Roles.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Block/Adminhtml/Roles/Tab/Users.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Mage_Api2_Model_Acl_Filter_Attribute_ResourcePermission and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Filter/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Mage_Api2_Model_Acl_Global_Rule_ResourcePermission and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Role.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Acl/Global/Rule/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Auth/Adapter/Oauth.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Multicall.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Request/Interpreter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource/Acl/Filter/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource/Acl/Global/Role.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<min, 0>|int<2, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource/Acl/Global/Role.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and float|int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource/Validator/Eav.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource/Validator/Fields.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Resource/Validator/Fields.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Api2/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Adminhtml/Catalog/Product/Edit/Tab/Attributes/Extend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Adminhtml/Catalog/Product/Edit/Tab/Attributes/Extend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Adminhtml/Catalog/Product/Edit/Tab/Bundle/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Adminhtml/Sales/Order/Items/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Adminhtml/Sales/Order/View/Items/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and array|int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Catalog/Product/View/Type/Bundle/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Block/Sales/Order/Items/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Attribute/Source/Price/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Indexer/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 0>|int<2, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Resource/Price/Index.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Bundle/Model/Sales/Order/Pdf/Items/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Captcha/Model/Zend.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Category/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Category/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Layer/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int<-1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Navigation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Compare/List.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Gallery.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List/Toolbar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List/Toolbar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and (int|string) is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List/Toolbar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string|false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List/Toolbar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List/Toolbar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/List/Upsell.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Attributes.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Options.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Options/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/View/Options/Type/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Product/Widget/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Seo/Sitemap/Tree/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Block/Widget/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Output.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product/Configuration.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Helper/Product/Type/Composite.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Category/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Image/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Image/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Image/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Image/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Rest/Admin/V1.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and true is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|int|string and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Api2/Product/Validator/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Attribute/Backend/Urlkey/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max>|string and int<min, -1>|int<1, max>|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Attribute/Backend/Sortby.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Indexer/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Category/Indexer/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<-1, 1> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Adapter/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Parser/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Convert/Parser/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Design.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Entity/Product/Attribute/Design/Options/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Indexer/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and (int|string) is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|string and float|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|int and float is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and float is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Layer/Filter/Price/Algorithm.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|false and int<-4, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and true is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Startdate/Specialprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Media/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Media/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Tierprice/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Attribute/Tierprice/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Flat/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Flat/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Flat/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Indexer/Eav.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Indexer/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Indexer/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 9,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Indexer/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Indexer/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Link/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Media/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Type/Text.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Option/Value.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Configurable/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Type/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Product/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array|string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Flat/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Category/Indexer/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Eav/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 11,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Eav/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Eav/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and bool is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string|null is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and float is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Layer/Filter/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 999> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Groupprice/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Media.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Backend/Urlkey.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Flat/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Flat/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and true is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Eav/Source.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 9999> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Indexer/Eav/Source.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Link/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option/Value.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Option/Value.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Product/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Resource/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 9,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|true and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between true and true is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/controllers/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Catalog/data/catalog_setup/data-install-1.6.0.0.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Aggregation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Catalog/Index/Kill/Flag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Eav.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Eav.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Minimalprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Tierprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Indexer/Tierprice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Indexer/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogIndex/Model/Resource/Price.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Helper/Minsaleqty.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Helper/Minsaleqty.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Indexer/Stock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Indexer/Stock.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 999> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Resource/Indexer/Stock/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogInventory/Model/Stock/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Resource/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogRule/Model/Rule/Condition/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Block/Advanced/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Block/Advanced/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Block/Autocomplete.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Block/Autocomplete.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Block/Autocomplete.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (float|int) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Block/Term.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Block/Term.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between object and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Fulltext/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Fulltext/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Indexer/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Indexer/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Indexer/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Advanced.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 0>|int<2, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 1>|int<3, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Fulltext/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/CatalogSearch/Model/Resource/Search/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 37,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/State/Jcb.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and false is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/State/Mastercard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 42,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/State/Mastercard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and false is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/State/Visa.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 46,
    'path' => __DIR__ . '/../app/code/core/Mage/Centinel/Model/State/Visa.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Crosssell.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Cart/Item/Renderer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Multishipping/Address/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Onepage/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Onepage/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max> and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Block/Onepage/Success.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Helper/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and true is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between false and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Coupon/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Customer/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Payment/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Payment/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Product/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Cart/Product/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Multishipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and true is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/Model/Type/Onepage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/CartController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/Multishipping/AddressController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/MultishippingController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/OnepageController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/OnepageController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Checkout/controllers/OnepageController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Resource/Page/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Block/Catalog/Media/Js/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Mediafallback.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Mediafallback.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Mediafallback.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Mediafallback.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productimg.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Helper/Productlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/ConfigurableSwatches/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Store/Switcher.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Block/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Front/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Request/Http.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Action.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Front.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Admin.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Controller/Varien/Router/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Exception.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/File/Storage/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Js.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Helper/Translate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/App/Emulation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cache.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|false and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Cookie.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Design/Package.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Domainpolicy.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Email/Template/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Encryption.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Storage.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Storage/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Storage/Directory/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Storage/Directory/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/AvailablePath.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/File/Validator/AvailablePath.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Input/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Layout/Update.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Db/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/File/Storage/Directory/Database.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Resource.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array and array is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup/Query/Modifier.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup/Query/Modifier.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Setup/Query/Modifier.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false|null and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false|null and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Translate/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Resource/Translate/String.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Session/Abstract/Varien.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and Mage_Directory_Model_Currency is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Store/Group.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Translate/Inline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and array is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and array|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 10,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Url/Rewrite/Request.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Validate/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Variable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/Model/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/functions.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 1> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/functions.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/functions.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, 8191>|int<8193, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/functions.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Core/functions.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cron/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Cron/Model/Resource/Schedule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CurrencySymbol/Model/System/Currencysymbol.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/CurrencySymbol/Model/System/Currencysymbol.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Account/Navigation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max>|string and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Address/Edit.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Address/Renderer/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Block/Address/Renderer/Default.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int<min, -1>|int<1, max>|string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string|null is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int<min, -1>|int<1, max>|string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Address/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string|null is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Api2/Customer/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Config/Share.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Adapter/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Adapter/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Convert/Parser/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max>|string and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between true and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Password.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Source/Store.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Customer/Attribute/Source/Website.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max>|string and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Address/Attribute/Backend/Region.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and true is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/Model/Resource/Setup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/controllers/AddressController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/sql/customer_setup/mysql4-data-upgrade-1.4.0.0.13-1.4.0.0.14.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/sql/customer_setup/mysql4-data-upgrade-1.4.0.0.7-1.4.0.0.8.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 18,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/sql/customer_setup/mysql4-data-upgrade-1.4.0.0.7-1.4.0.0.8.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Customer/sql/customer_setup/mysql4-data-upgrade-1.4.0.0.8-1.4.0.0.9.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Batch/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Container/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Varien_Simplexml_Element|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Profile/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Convert/Profile/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 255> and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Session/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Dataflow/Model/Session/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Block/Adminhtml/Frontend/Currency/Base.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Country.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency/Import/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency/Import/Currencyconverterapi.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency/Import/Fixerio.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Currency/Import/Webservicex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Resource/Country/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Resource/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Directory/Model/Resource/Currency.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Block/Adminhtml/Catalog/Product/Edit/Tab/Downloadable/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Block/Catalog/Product/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 12,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/Download.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/Download.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|false and int<-4, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Helper/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Api/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Api/Validator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Purchased.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Link/Purchased/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array|null and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 10,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Product/Type.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Resource/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/Model/Resource/Link.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/Adminhtml/Downloadable/FileController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/Adminhtml/Downloadable/Product/EditController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/DownloadController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 10,
    'path' => __DIR__ . '/../app/code/core/Mage/Downloadable/controllers/DownloadController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Date.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Datetime.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Attribute/Data/Text.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Entity.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Adapter/Entity.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<-1, 1> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Convert/Parser/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Frontend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Frontend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Set.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<-1, 1> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Boolean.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Entity/Attribute/Source/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Entity/Attribute.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Attribute/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Eav/Model/Resource/Form/Fieldset/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Block/Message/Inline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Api/V2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Entity/Attribute/Backend/Boolean/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Message.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GiftMessage/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/GoogleAnalytics/Block/Ga.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/GoogleAnalytics/Block/Ga.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/GoogleAnalytics/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Block/Adminhtml/Export/Filter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array and array is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and (int|string) is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Adapter/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Adapter/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<min, 0>|int<2, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 0>|int<2, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Customer/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 14,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<min, 0>|int<2, max> is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<min, 1>|int<3, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string|false|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<min, -2>|int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Configurable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/Model/Import/Entity/Product/Type/Grouped.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ImportExport/controllers/Adminhtml/ImportController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Block/Adminhtml/Notifications.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Event.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Process.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Index/Model/Resource/Event.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Block/Locale.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer/Console.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Installer/Db/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Install/Model/Wizard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Customer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Log.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array|string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Visitor/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Log/Model/Resource/Visitor/Online/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Resource/Problem/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Resource/Queue.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Resource/Queue/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Resource/Subscriber/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and true is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 1>|int<3, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 1>|int<4, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 2>|int<4, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/Model/Subscriber.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/controllers/SubscriberController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Newsletter/controllers/SubscriberController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Server.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/Model/Token.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/AuthorizeController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/AuthorizedTokensController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/controllers/Adminhtml/Oauth/ConsumerController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Oauth/controllers/Customer/TokenController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Header.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Pager.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (float|int) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Topmenu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Topmenu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Html/Topmenu.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Redirect.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Switch.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Template/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Page/Block/Template/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Block/Authorizenet/Form/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 9,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paygate/Model/Authorizenet.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Block/Info/Container.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<-9, 9> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 1> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 0>|int<2, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Cc.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Free.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Method/Free.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Payment/Model/Recurring/Profile.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Field/Country.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Adminhtml/System/Config/Fieldset/Global.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Bml/Banners.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Express/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Express/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Express/Shortcut.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Iframe.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Iframe.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Iframe.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Block/Logo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Controller/Express/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|false and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Api/Standard.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Cart.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Direct.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Express/Checkout.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Info.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array|string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Ipn.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Method/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowlink.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payflowpro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Pro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/Model/Pro.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/controllers/PayflowController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Paypal/controllers/PayflowadvancedController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/PaypalUk/Model/Api/Nvp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Observer/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Persistent/Model/Observer/Session.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Email.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Email.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Resource/Price/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/ProductAlert/Model/Resource/Stock/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Block/Entity/Detailed.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|null is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rating/Model/Resource/Rating/Option/Vote/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Grouped/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Grouped/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Event.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool|int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Product/Downloads/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Quote/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Report/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Report/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Report/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Report/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Report/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|false and int|false is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Report/Product/Viewed/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Resource/Report/Product/Viewed/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Test.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Test.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Reports/Model/Totals.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Customer/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Block/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array|null and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Resource/Review/Product/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/Model/Review.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Review/controllers/ProductController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and null is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Block/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/controllers/CatalogController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/controllers/CatalogController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rss/controllers/OrderController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|int|string and float|int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and float|int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Condition/Product/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 999> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Resource/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Rule/Model/Resource/Rule/Collection/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Billing/Agreements.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Totals.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Totals.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Totals.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Order/Totals.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Block/Recurring/Profile/View.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Helper/Guest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Api2/Order/Address/Rest.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Billing/Agreement.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Entity/Order/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 8,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Api.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (float|int) and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Creditmemo/Total/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Invoice/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Payment/Transaction.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (float|int) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Pdf/Shipment/Packaging.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Shipment/Track.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Order/Status/History.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Payment/Method/Billing/AgreementAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and true is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Address/Total/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Quote/Item/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Helper/Mysql4.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Attribute/Backend/Billing.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Attribute/Backend/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Order/Status.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|false and int|false is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Bestsellers/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Bestsellers/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Invoiced/Collection/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Order/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Refunded/Collection/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Resource/Report/Shipping/Collection/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sales/Model/Service/Order.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Coupon/Massgenerator.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Resource/Report/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Address.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Product/Attribute/Assigned.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/SalesRule/Model/Rule/Condition/Product/Combine.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Block/Tracking/Popup.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string|false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Flatrate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Flatrate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and (float|int) is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Resource/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Resource/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Resource/Carrier/Tablerate.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/Model/Shipping.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Shipping/controllers/TrackingController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Resource/Catalog/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Resource/Catalog/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Resource/Cms/Page.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Sitemap/Model/Sitemap.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (float|int) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/All.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/All.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (float|int) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Customer/Tags.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Customer/Tags.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (float|int) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Popular.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Block/Popular.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Indexer/Summary.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array|string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Customer/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/Model/Resource/Tag/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tag/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 8,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and Mage_Sales_Model_Order is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and null is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Calculation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Calculation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Calculation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Class/Source/Product.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 24,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Config.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and (int|string) is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Resource/Calculation.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Resource/Report/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Subtotal.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and null is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Tax/Model/Sales/Total/Quote/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract/Backend/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and array|bool is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 17,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between SimpleXMLElement and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between array|string|false and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and bool is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and float|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and string is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/Label/Pdf/PageBuilder.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Varien_Object|null and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 16,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Varien_Object|null and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 26,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Varien_Object|null and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 4>|int<6, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool|int|Mage_Core_Model_Store|string|null and null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Helper/Data.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Attribute/Backend/Weee/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Weee/Model/Resource/Tax.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Block/Adminhtml/Widget/Instance/Edit/Chooser/Block.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Block/Adminhtml/Widget/Instance/Edit/Chooser/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Block/Adminhtml/Widget/Options.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Model/Widget.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Model/Widget/Instance.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/Model/Widget/Instance.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Widget/controllers/Adminhtml/Widget/InstanceController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 1> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Block/Links.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 900>|int<902, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Controller/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Item.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string|null and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Observer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Resource/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/Model/Wishlist.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 900>|int<902, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/IndexController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/SharedController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, -1>|int<1, max> and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/SharedController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 900>|int<902, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/code/core/Mage/Wishlist/controllers/SharedController.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/api/userroles.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/product/composite/fieldset/options/type/checkbox.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/product/composite/fieldset/options/type/multi.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/product/edit/bundle.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/creditmemo/create/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/creditmemo/view/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/invoice/create/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/invoice/view/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/order/view/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/shipment/create/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/bundle/sales/shipment/view/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/form/renderer/fieldset/element.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/attribute/js.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/attribute/options.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/composite/fieldset/options/type/date.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/composite/fieldset/options/type/select.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/composite/fieldset/options/type/text.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/edit/action/inventory.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/edit/action/inventory.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/tab/inventory.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/catalog/product/tab/inventory.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/customer/edit/tab/account/form/renderer/group.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/customer/edit/tab/account/form/renderer/group.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/customer/sales/order/create/address/form/renderer/vat.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/customer/tab/addresses.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/customer/tab/view/sales.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and (int|string) is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/dashboard/graph.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/dashboard/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and false is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/dashboard/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/dashboard/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/eav/attribute/options.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/giftmessage/form.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and false is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/newsletter/preview/store.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/newsletter/preview/store.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 3> and int<-1, 2> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/notification/toolbar.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 3> and int<0, 3> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/notification/toolbar.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/payment/form/cc.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/payment/form/cc.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/payment/form/ccsave.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/payment/form/ccsave.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/permissions/userroles.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/rating/detailed.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/rating/detailed.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and false is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|string|null is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and false is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/report/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<2, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/billing/method/form.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/billing/method/form.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/data.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|false is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/form/address.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/sidebar/items.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and false is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/store/select.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/create/totals/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/shipment/packaging/packed.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/shipment/packaging/popup.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 1> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/shipment/view/tracking.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/order/view/history.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/payment/form/billing/agreement.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, 2> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/recurring/profile/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, 2> and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/sales/recurring/profile/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and false is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/store/switcher.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool and false is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int|null is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/store/switcher/enhanced.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/store/switcher/form/renderer/fieldset/element.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/config/tabs.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/convert/profile/process.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/currency/rate/matrix.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and (int|string) is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/currency/rate/matrix.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/email/template/edit.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/email/template/edit.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/system/store/tree.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/tax/rate/title.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/widget/form/element.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/widget/form/element/gallery.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/widget/form/renderer/fieldset/element.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/widget/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<-1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/widget/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/adminhtml/base/default/template/widget/grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/adminhtml/openmage/default/template/eav/attribute/options.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 9,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/catalog/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/catalog/product/view/option_tierprices.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/catalog/product/view/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/catalog/product/view/type/bundle/option/checkbox.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/catalog/product/view/type/bundle/option/multi.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 10,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/rss/catalog/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/sales/order/creditmemo/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/sales/order/invoice/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/sales/order/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/bundle/sales/order/shipment/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 9> and int is not allowed.',
    'count' => 5,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<min, -1>|int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/new.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/new.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/view/options/type/date.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/view/options/type/select.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/view/options/type/text.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/view/tierprices.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/widget/new/content/new_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/widget/new/content/new_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/product/widget/new/content/new_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/catalog/rss/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/centinel/authentication.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/cart/sidebar.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 2> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/multishipping/address/select.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/multishipping/billing.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/multishipping/shipping.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/multishipping/shipping.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/payment/methods.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/shipping_method/available.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/onepage/shipping_method/available.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/total/default.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/total/nominal.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/checkout/total/nominal.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/customer/form/edit.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/customer/widget/gender.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/customer/widget/name.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/directory/currency.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/page/switch/flags.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|null and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/page/switch/languages.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/page/switch/stores.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/payment/form/cc.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/payment/form/cc.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/payment/form/ccsave.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/payment/form/ccsave.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/home_product_compared.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/home_product_compared.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/home_product_viewed.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/home_product_viewed.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/compared/content/compared_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/compared/content/compared_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/compared/content/compared_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/viewed/content/viewed_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/viewed/content/viewed_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/reports/widget/viewed/content/viewed_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, 2> and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/sales/recurring/profile/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 1> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/base/default/template/tag/customer/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<1, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/bundle/sales/order/items/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, 9> and int is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/compare/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float and float is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/price.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/widget/new/content/new_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/widget/new/content/new_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/catalog/product/widget/new/content/new_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/checkout/cart/sidebar.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|false and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/checkout/multishipping/billing.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/configurableswatches/catalog/product/list/swatches.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/configurableswatches/catalog/product/view/type/options/configurable/swatches.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/customer/form/edit.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/directory/currency.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/email/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/email/catalog/product/list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/email/catalog/product/new.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/email/catalog/product/new.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/page/html/topmenu/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/page/html/topmenu/renderer.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/payment/form/cc.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/payment/form/cc.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between (int|string) and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/payment/form/ccsave.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/payment/form/ccsave.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/persistent/checkout/onepage/login.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/reports/widget/compared/content/compared_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/reports/widget/compared/content/compared_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/reports/widget/compared/content/compared_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/reports/widget/viewed/content/viewed_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/reports/widget/viewed/content/viewed_grid.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/reports/widget/viewed/content/viewed_list.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, 2> and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/frontend/rwd/default/template/sales/recurring/profile/view.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/install/default/default/template/install/begin.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../app/design/install/default/default/template/install/db/main.phtml',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between list<mixed>|string|false and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../errors/report.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../errors/report.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Archive.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between float|int and int<0, max> is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 9,
    'path' => __DIR__ . '/../lib/Mage/Archive/Tar.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, 100> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../lib/Mage/Cache/Backend/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Socket.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/HTTP/Client/Socket.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Args.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<1, max> and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/System/Args.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|false and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/System/Args.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Args.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Mage/System/Ftp.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Magento/Profiler.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Magento/Profiler/OutputAbstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Convert/Container/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int<0, max> is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Convert/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Varien/Convert/Parser/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int<0, max> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Db.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Collection/Filesystem.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Collection.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Fieldset.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Gallery.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Multiline.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Radios.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and int<0, 23> is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Time.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int|string and int<0, 59> is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Data/Form/Element/Time.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Mysqli.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between bool|int and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 12,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 18,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Adapter/Pdo/Mysql.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Ddl/Table.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Db/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Db/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string|null and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Select.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and (float|int) is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Db/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Db/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../lib/Varien/Db/Tree.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Debug.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int|string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Event/Observer/Cron.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and mixed is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Csv.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and mixed is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and null is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/File/Uploader/Image.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max> and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template/Tokenizer/Parameter.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../lib/Varien/Filter/Template/Tokenizer/Variable.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<min, 99>|int<101, max> and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Http/Adapter/Curl.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int and int is not allowed.',
    'count' => 6,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and int is not allowed.',
    'count' => 4,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 7,
    'path' => __DIR__ . '/../lib/Varien/Image/Adapter/Gd2.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/Abstract.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between Varien_Io_File|null and string is not allowed.',
    'count' => 2,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between int<0, max>|false and int is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 3,
    'path' => __DIR__ . '/../lib/Varien/Io/File.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between mixed and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../shell/indexer.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Loose comparison via "==" between string and string is not allowed.',
    'count' => 1,
    'path' => __DIR__ . '/../shell/indexer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
