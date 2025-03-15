<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Base;

trait ModulesTrait
{
    public static array $disabledModules = [
        'Mage_Centinal',
        'Mage_Tag',
    ];

    final public function provideAllModules(): array
    {
        return [
            'Mage_Admin',
            'Mage_Adminhtml',
            'Mage_AdminNotification',
            'Mage_Api',
            'Mage_Api2',
            'Mage_Authorizenet',
            'Mage_Bundle',
            'Mage_Captcha',
            'Mage_Catalog',
            'Mage_CatalogIndex',
            'Mage_CatalogInventory',
            'Mage_CatalogRule',
            'Mage_CatalogSearch',
            'Mage_Centinal',
            'Mage_Checkout',
            'Mage_Cms',
            'Mage_ConfigurableSwatches',
            'Mage_Contacts',
            'Mage_Core',
            'Mage_Cron',
            'Mage_CurrencySymbol',
            'Mage_Customer',
            'Mage_Dataflow',
            'Mage_Directory',
            'Mage_Downloadable',
            'Mage_Eav',
            'Mage_GiftMessage',
            'Mage_GoogleAnalytics',
            'Mage_GoogleCheckout',
            'Mage_ImportExport',
            'Mage_Index',
            'Mage_Install',
            'Mage_Log',
            'Mage_Media',
            'Mage_Newsletter',
            'Mage_Oauth',
            'Mage_Page',
            'Mage_Paygate',
            'Mage_Payment',
            'Mage_Paypal',
            'Mage_PaypalUk',
            'Mage_Persistent',
            'Mage_ProductAlert',
            'Mage_Rating',
            'Mage_Reports',
            'Mage_Review',
            'Mage_Rss',
            'Mage_Rule',
            'Mage_Sales',
            'Mage_SalesRule',
            'Mage_Shipping',
            'Mage_Sitemap',
            'Mage_Tag',
            'Mage_Tax',
            'Mage_Uploader',
            'Mage_Usa',
            'Mage_Weee',
            'Mage_Widget',
            'Mage_Wishlist',
        ];
    }

    public function provideAllActiveModules(): array
    {
        $disabled   = static::$disabledModules;
        $modules    = $this->provideAllModules();

        foreach ($modules as $idx => $module) {
            if (in_array($module, $disabled)) {
                unset($modules[$idx]);
            }
        }
        return $modules;
    }
}
