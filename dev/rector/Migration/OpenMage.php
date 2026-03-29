<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration;

use Mage;
use Mage_Core_Model_Store;
use Rector\Arguments\ValueObject\ReplaceArgumentDefaultValue;

final class OpenMage
{
    /**
     * @return ReplaceArgumentDefaultValue[]
     */
    public static function replaceStoreConfigPathsWithConstants(): array
    {
        $map = [];
        $paths = [
            'general/locale/code'           => 'Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE',
            'general/locale/timezone'       => 'Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE',
            #'web/default/cms_home_page'     => 'Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE',
            #'web/default/cms_no_cookies'    => 'Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE',
            #'web/default/cms_no_route'      => 'Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE',
        ];

        foreach ($paths as $old => $new) {
            $map[] = new ReplaceArgumentDefaultValue(Mage::class, 'getStoreConfig', 0, $old, $new);
            #$map[] = new ReplaceArgumentDefaultValue(Mage_Core_Model_Store::class, 'getConfig', 0, $old, $new);
        }

        return $map;
    }
}
