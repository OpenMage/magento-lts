<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main;
use Mage_Adminhtml_Block_Page_Footer;
use Mage_Adminhtml_Block_Sales_Order_Create_Form_Account;
use Mage_Adminhtml_Block_Template;
use Mage_Adminhtml_Block_Widget_Container;
use Mage_Adminhtml_Block_Widget_Form;
use Mage_Adminhtml_Block_Widget_Grid;
use Mage_Adminhtml_Controller_Action;
use Rector\Arguments\ValueObject\ReplaceArgumentDefaultValue;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Adminhtml
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main::class, '_getSetData', '_getAttributeSet'),
            new MethodCallRename(Mage_Adminhtml_Block_Page_Footer::class, 'setBugreportUrl', 'setReportIssuesUrl'),
            new MethodCallRename(Mage_Adminhtml_Block_Page_Footer::class, 'getBugreportUrl', 'getReportIssuesUrl'),
            new MethodCallRename(Mage_Adminhtml_Block_Page_Footer::class, 'setConnectWithMagentoUrl', 'setOpenMageProjectUrl'),
            new MethodCallRename(Mage_Adminhtml_Block_Page_Footer::class, 'getConnectWithMagentoUrl', 'getOpenMageProjectUrl'),
            new MethodCallRename(Mage_Adminhtml_Block_Sales_Order_Create_Form_Account::class, 'getCustomerData', 'getFormValues'),
            new MethodCallRename(Mage_Adminhtml_Block_Template::class, 'isOutputEnabled', 'isModuleOutputEnabled'),
            new MethodCallRename(Mage_Adminhtml_Block_Widget_Form::class, 'getFormObject', 'getForm'),
            new MethodCallRename(Mage_Adminhtml_Controller_Action::class, '_sendUploadResponse', '_prepareDownloadResponse'),
        ];
    }

    public const BUTTON_TYPE_ADD        = 'add';

    public const BUTTON_TYPE_BACK       = 'back';

    public const BUTTON_TYPE_CANCEL     = 'cancel';

    public const BUTTON_TYPE_CLOSE      = 'close';

    public const BUTTON_TYPE_DELETE     = 'delete';

    public const BUTTON_TYPE_PRINT      = 'print';

    public const BUTTON_TYPE_RESET      = 'reset';

    public const BUTTON_TYPE_SAVE       = 'save';

    public const BUTTON_TYPE_SAVE_EDIT  = 'save-edit';

    public const BUTTON_TYPE_VOID       = 'void';

    /**
     * @return ReplaceArgumentDefaultValue[]
     */
    public static function replaceArgumentDefaultValue(): array
    {
        return [
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'add', 'self::BUTTON_TYPE_ADD'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'back', 'self::BUTTON_TYPE_BACK'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'cancel', 'self::BUTTON_TYPE_CANCEL'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'close', 'self::BUTTON_TYPE_CLOSE'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'delete', 'self::BUTTON_TYPE_DELETE'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'print', 'self::BUTTON_TYPE_PRINT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'reset', 'self::BUTTON_TYPE_RESET'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'save', 'self::BUTTON_TYPE_SAVE'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'savecontinue', 'self::BUTTON_TYPE_SAVE_EDIT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'saveandcontinue', 'self::BUTTON_TYPE_SAVE_EDIT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'save_and_continue', 'self::BUTTON_TYPE_SAVE_EDIT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'save_and_edit', 'self::BUTTON_TYPE_SAVE_EDIT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_removeButton', 0, 'void', 'self::BUTTON_TYPE_VOID'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'add', 'self::BUTTON_TYPE_ADD'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'back', 'self::BUTTON_TYPE_BACK'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'cancel', 'self::BUTTON_TYPE_CANCEL'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'close', 'self::BUTTON_TYPE_CLOSE'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'delete', 'self::BUTTON_TYPE_DELETE'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'print', 'self::BUTTON_TYPE_PRINT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'reset', 'self::BUTTON_TYPE_RESET'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'save', 'self::BUTTON_TYPE_SAVE'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'savecontinue', 'self::BUTTON_TYPE_SAVE_EDIT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'saveandcontinue', 'self::BUTTON_TYPE_SAVE_EDIT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'save_and_continue', 'self::BUTTON_TYPE_SAVE_EDIT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'save_and_edit', 'self::BUTTON_TYPE_SAVE_EDIT'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Container::class, '_updateButton', 0, 'void', 'self::BUTTON_TYPE_VOID'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Grid::class, 'setDefaultDir', 0, 'asc', 'ASC'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Grid::class, 'setDefaultDir', 0, 'desc', 'DESC'),
        ];
    }
}
