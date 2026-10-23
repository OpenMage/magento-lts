<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Controller;

use Generator;
use Mage_Adminhtml_Report_CustomerController;
use Mage_Adminhtml_Report_ProductController;
use Mage_Adminhtml_Report_SalesController;
use Mage_Adminhtml_Report_ShopcartController;
use Mage_Adminhtml_Report_TagController;
use Mage_Adminhtml_ReportController;
use Mage_Adminhtml_Sales_OrderController;
use Mage_Adminhtml_System_ConfigController;
use Mage_Adminhtml_Tax_RateController;
use Mage_Api2_Adminhtml_Api2_AttributeController;
use Mage_Api2_Adminhtml_Api2_RoleController;
use Mage_Usa_Adminhtml_UspsController;

trait ActionAclTrait
{
    /**
     * Controller, action, request params, resources ticked in the role editor, expected result.
     *
     * Each denial is paired with the role that should keep access, so a fix that
     * locks out a correctly configured role fails as loudly as a bypass.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function provideActionAclData(): Generator
    {
        $order = Mage_Adminhtml_Sales_OrderController::class;
        $view = ['sales/order/actions/view'];

        foreach (['massCancel' => 'cancel', 'massHold' => 'hold', 'massUnhold' => 'unhold'] as $action => $leaf) {
            yield "order {$action} denied without {$leaf}" => [$order, $action, [], $view, false];
            yield "order {$action} allowed with {$leaf}" => [$order, $action, [], ["sales/order/actions/{$leaf}"], true];
        }

        yield 'order voidPayment denied without invoice' => [$order, 'voidPayment', [], $view, false];
        yield 'order voidPayment allowed with invoice' => [$order, 'voidPayment', [], ['sales/order/actions/invoice'], true];
        yield 'order address denied without edit' => [$order, 'address', [], $view, false];
        yield 'order addressSave denied without edit' => [$order, 'addressSave', [], $view, false];
        yield 'order addressSave allowed with edit' => [$order, 'addressSave', [], ['sales/order/actions/edit'], true];
        yield 'order pdfcreditmemos denied without credit memos' => [$order, 'pdfcreditmemos', [], $view, false];
        yield 'order pdfcreditmemos allowed with order credit memos' => [$order, 'pdfcreditmemos', [], ['sales/order/actions/creditmemo'], true];
        yield 'order pdfcreditmemos allowed with credit memo grid' => [$order, 'pdfcreditmemos', [], ['sales/order', 'sales/creditmemo'], true];
        yield 'order pdfdocs still allowed to order viewers' => [$order, 'pdfdocs', [], $view, true];
        yield 'order grid still allowed to order viewers' => [$order, 'index', [], $view, true];
        yield 'order export still allowed to order viewers' => [$order, 'exportCsv', [], $view, true];
        yield 'order cancel unchanged' => [$order, 'cancel', [], $view, false];

        $shopcart = Mage_Adminhtml_Report_ShopcartController::class;
        yield 'abandoned cart export denied to products-in-cart' => [$shopcart, 'exportAbandonedCsv', [], ['report/shopcart/product'], false];
        yield 'abandoned cart export allowed with abandoned' => [$shopcart, 'exportAbandonedExcel', [], ['report/shopcart/abandoned'], true];
        yield 'products in cart export allowed with product' => [$shopcart, 'exportProductCsv', [], ['report/shopcart/product'], true];

        $tagReport = Mage_Adminhtml_Report_TagController::class;
        yield 'tag customer detail denied to popular' => [$tagReport, 'customerDetail', [], ['report/tags/popular'], false];
        yield 'tag customer detail export denied to popular' => [$tagReport, 'exportCustomerDetailCsv', [], ['report/tags/popular'], false];
        yield 'tag customer detail allowed with customer' => [$tagReport, 'customerDetail', [], ['report/tags/customer'], true];
        yield 'tag detail allowed with popular' => [$tagReport, 'tagDetail', [], ['report/tags/popular'], true];
        yield 'tag product detail denied to popular' => [$tagReport, 'productDetail', [], ['report/tags/popular'], false];

        $productReport = Mage_Adminhtml_Report_ProductController::class;
        yield 'downloads report denied to low stock' => [$productReport, 'downloads', [], ['report/products/lowstock'], false];
        yield 'downloads export denied to low stock' => [$productReport, 'exportDownloadsCsv', [], ['report/products/lowstock'], false];
        yield 'downloads report allowed with downloads' => [$productReport, 'downloads', [], ['report/products/downloads'], true];
        yield 'viewed export denied to sold' => [$productReport, 'exportViewedCsv', [], ['report/products/sold'], false];

        $salesReport = Mage_Adminhtml_Report_SalesController::class;
        yield 'bestsellers export denied to sales report' => [$salesReport, 'exportBestsellersCsv', [], ['report/salesroot/sales'], false];
        yield 'bestsellers export allowed with bestsellers' => [$salesReport, 'exportBestsellersCsv', [], ['report/products/bestsellers'], true];
        yield 'tax report export denied to sales report' => [$salesReport, 'exportTaxCsv', [], ['report/salesroot/sales'], false];
        yield 'sales report export allowed with sales' => [$salesReport, 'exportSalesExcel', [], ['report/salesroot/sales'], true];

        $customerReport = Mage_Adminhtml_Report_CustomerController::class;
        yield 'customer orders export denied to new accounts' => [$customerReport, 'exportOrdersCsv', [], ['report/customers/accounts'], false];
        yield 'customer orders export allowed with orders' => [$customerReport, 'exportOrdersCsv', [], ['report/customers/orders'], true];

        yield 'search terms export denied to other reports' => [Mage_Adminhtml_ReportController::class, 'exportSearchCsv', [], ['report/customers/accounts'], false];
        yield 'search terms export allowed with search' => [Mage_Adminhtml_ReportController::class, 'exportSearchCsv', [], ['report/search'], true];

        $restRoles = Mage_Api2_Adminhtml_Api2_RoleController::class;
        yield 'rest role new denied to delete' => [$restRoles, 'new', [], ['system/api/rest_roles/delete'], false];
        yield 'rest role create denied to edit' => [$restRoles, 'save', [], ['system/api/rest_roles/edit'], false];
        yield 'rest role create allowed with add' => [$restRoles, 'save', [], ['system/api/rest_roles/add'], true];
        yield 'rest role update denied to add' => [$restRoles, 'save', ['id' => '3'], ['system/api/rest_roles/add'], false];
        yield 'rest role update allowed with edit' => [$restRoles, 'save', ['id' => '3'], ['system/api/rest_roles/edit'], true];
        yield 'rest role delete denied to add' => [$restRoles, 'delete', ['id' => '3'], ['system/api/rest_roles/add'], false];
        yield 'rest role delete allowed with delete' => [$restRoles, 'delete', ['id' => '3'], ['system/api/rest_roles/delete'], true];
        yield 'rest role edit page still allowed to any role holder' => [$restRoles, 'edit', ['id' => '3'], ['system/api/rest_roles/add'], true];

        $restAttributes = Mage_Api2_Adminhtml_Api2_AttributeController::class;
        yield 'rest attributes denied to rest roles' => [$restAttributes, 'index', [], ['system/api/rest_roles/add'], false];
        yield 'rest attributes save denied to rest roles' => [$restAttributes, 'save', [], ['system/api/rest_roles/add'], false];
        yield 'rest attributes save denied without edit' => [$restAttributes, 'save', [], ['system/api/rest_attributes'], false];
        yield 'rest attributes save allowed with edit' => [$restAttributes, 'save', [], ['system/api/rest_attributes/edit'], true];
        yield 'rest attributes page allowed with attributes' => [$restAttributes, 'edit', [], ['system/api/rest_attributes'], true];

        $config = Mage_Adminhtml_System_ConfigController::class;
        yield 'table rates export denied to design section' => [$config, 'exportTablerates', [], ['system/config/design'], false];
        yield 'table rates export allowed with shipping methods' => [$config, 'exportTablerates', [], ['system/config/carriers'], true];
        yield 'config edit unchanged for design section' => [$config, 'edit', [], ['system/config/design'], true];

        yield 'usps tools denied to design section' => [Mage_Usa_Adminhtml_UspsController::class, 'testconnection', [], ['system/config/design'], false];
        yield 'usps tools allowed with shipping methods' => [Mage_Usa_Adminhtml_UspsController::class, 'createdimensions', [], ['system/config/carriers'], true];

        $taxRate = Mage_Adminhtml_Tax_RateController::class;
        yield 'tax rate import denied without import export' => [$taxRate, 'importPost', [], ['sales/tax/rates'], false];
        yield 'tax rate import allowed with import export' => [$taxRate, 'importPost', [], ['sales/tax/import_export'], true];
        yield 'tax rate grid export unchanged' => [$taxRate, 'exportCsv', [], ['sales/tax/rates'], true];
    }

    /**
     * Action, whether a reorder is in progress, resources, expected result.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function provideOrderCreateAclData(): Generator
    {
        foreach (['loadBlock', 'processData', 'addConfigured', 'configureProductToAdd', 'configureQuoteItems', 'showUpdateResult'] as $action) {
            yield "{$action} denied to order viewer" => [$action, false, ['sales/order/actions/view'], false];
            yield "{$action} allowed with create" => [$action, false, ['sales/order/actions/create'], true];
            yield "{$action} allowed with reorder during a reorder" => [$action, true, ['sales/order/actions/reorder'], true];
            yield "{$action} denied with reorder outside a reorder" => [$action, false, ['sales/order/actions/reorder'], false];
        }

        yield 'start denied to order viewer' => ['start', false, ['sales/order/actions/view'], false];
        yield 'start allowed with create' => ['start', false, ['sales/order/actions/create'], true];
        yield 'index allowed with create' => ['index', false, ['sales/order/actions/create'], true];
        yield 'save during reorder needs reorder' => ['save', true, ['sales/order/actions/create'], false];
    }

    /**
     * Action, resources, expected result.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function provideOrderEditAclData(): Generator
    {
        foreach (['start', 'index', 'loadBlock', 'save', 'cancel'] as $action) {
            yield "{$action} denied to create" => [$action, ['sales/order/actions/create'], false];
            yield "{$action} allowed with edit" => [$action, ['sales/order/actions/edit'], true];
        }
    }

    /**
     * Action, request params, statuses of the requested tags, resources, expected result.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function provideTagAclData(): Generator
    {
        $pendingOnly = ['catalog/tag/pending'];
        $all = ['catalog/tag/all'];
        $pending = 0;
        $approved = 1;

        yield 'all tags grid denied to pending' => ['index', [], [], $pendingOnly, false];
        yield 'all tags ajax grid denied to pending' => ['ajaxGrid', [], [], $pendingOnly, false];
        yield 'all tags grid allowed with all' => ['index', [], [], $all, true];
        yield 'pending grid denied to all' => ['pending', [], [], $all, false];
        yield 'pending ajax grid allowed with pending' => ['ajaxPendingGrid', [], [], $pendingOnly, true];
        yield 'new tag denied to pending' => ['new', [], [], $pendingOnly, false];
        yield 'edit approved tag denied to pending' => ['edit', ['tag_id' => '5'], [5 => $approved], $pendingOnly, false];
        yield 'edit pending tag allowed with pending' => ['edit', ['tag_id' => '5'], [5 => $pending], $pendingOnly, true];
        yield 'delete approved tag denied to pending' => ['delete', ['tag_id' => '5'], [5 => $approved], $pendingOnly, false];
        yield 'delete missing tag denied to pending' => ['delete', ['tag_id' => '5'], [], $pendingOnly, false];
        yield 'mass delete with an approved tag denied to pending' => ['massDelete', ['tag' => ['5', '6']], [5 => $pending, 6 => $approved], $pendingOnly, false];
        yield 'mass status of pending tags allowed with pending' => ['massStatus', ['tag' => ['5', '6']], [5 => $pending, 6 => $pending], $pendingOnly, true];
        yield 'delete approved tag allowed with all' => ['delete', ['tag_id' => '5'], [5 => $approved], $all, true];
    }

    /**
     * Posted data, type of the existing class, resources, expected result.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function provideTaxClassAclData(): Generator
    {
        $customer = ['sales/tax/classes_customer'];
        $product = ['sales/tax/classes_product'];

        yield 'product class denied to customer classes' => [['class_type' => 'PRODUCT'], null, $customer, false];
        yield 'customer class allowed with customer classes' => [['class_type' => 'CUSTOMER'], null, $customer, true];
        yield 'product class allowed with product classes' => [['class_type' => 'PRODUCT'], null, $product, true];
        yield 'converting a product class denied to customer classes' => [['class_type' => 'CUSTOMER', 'class_id' => '2'], 'PRODUCT', $customer, false];
        yield 'renaming a product class denied to customer classes' => [['class_id' => '2'], 'PRODUCT', $customer, false];
        yield 'editing a customer class allowed with customer classes' => [['class_type' => 'CUSTOMER', 'class_id' => '3'], 'CUSTOMER', $customer, true];
        yield 'either type allowed with both' => [['class_type' => 'PRODUCT', 'class_id' => '3'], 'CUSTOMER', [...$customer, ...$product], true];
    }
}
