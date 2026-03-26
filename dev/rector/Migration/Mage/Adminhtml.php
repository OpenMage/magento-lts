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

    /**
     * @return ReplaceArgumentDefaultValue[]
     */
    public static function replaceArgumentDefaultValue(): array
    {
        return [
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Grid::class, 'setDefaultDir', 0, 'asc', 'ASC'),
            new ReplaceArgumentDefaultValue(Mage_Adminhtml_Block_Widget_Grid::class, 'setDefaultDir', 0, 'desc', 'DESC'),
        ];
    }
}
