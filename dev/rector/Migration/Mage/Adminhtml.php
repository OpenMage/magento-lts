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
use OpenMage\Rector\ValueObject\ClassConstant;
use OpenMage\Rector\ValueObject\ReplaceArgumentValue;
use PHPStan\Type\BooleanType;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\TypeDeclaration\ValueObject\AddReturnTypeDeclaration;

final class Adminhtml
{
    /**
     * @return AddReturnTypeDeclaration[]
     */
    public static function addReturnTypeDeclaration(): array
    {
        return [
            new AddReturnTypeDeclaration(Mage_Adminhtml_Controller_Action::class, '_isAllowed', new BooleanType()),
        ];
    }

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

    /**
     * @return ReplaceArgumentValue[]
     */
    public static function replaceArgumentValue(): array
    {
        $container = Mage_Adminhtml_Block_Widget_Container::class;
        $buttonTypes = [
            'add'               => 'BUTTON_TYPE_ADD',
            'back'              => 'BUTTON_TYPE_BACK',
            'cancel'            => 'BUTTON_TYPE_CANCEL',
            'close'             => 'BUTTON_TYPE_CLOSE',
            'delete'            => 'BUTTON_TYPE_DELETE',
            'print'             => 'BUTTON_TYPE_PRINT',
            'reset'             => 'BUTTON_TYPE_RESET',
            'save'              => 'BUTTON_TYPE_SAVE',
            'savecontinue'      => 'BUTTON_TYPE_SAVE_EDIT',
            'saveandcontinue'   => 'BUTTON_TYPE_SAVE_EDIT',
            'save_and_continue' => 'BUTTON_TYPE_SAVE_EDIT',
            'save_and_edit'     => 'BUTTON_TYPE_SAVE_EDIT',
            'void'              => 'BUTTON_TYPE_VOID',
        ];

        $replacements = [];
        foreach (['_addButton', '_removeButton', '_updateButton'] as $method) {
            foreach ($buttonTypes as $before => $constant) {
                $replacements[] = new ReplaceArgumentValue($container, $method, 0, $before, new ClassConstant($container, $constant));
            }
        }

        $replacements[] = new ReplaceArgumentValue(Mage_Adminhtml_Block_Widget_Grid::class, 'setDefaultDir', 0, 'asc', 'ASC');
        $replacements[] = new ReplaceArgumentValue(Mage_Adminhtml_Block_Widget_Grid::class, 'setDefaultDir', 0, 'desc', 'DESC');

        return $replacements;
    }
}
