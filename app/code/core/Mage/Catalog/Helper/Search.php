<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog search helper
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Search extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Catalog';

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function isNotEnabled(): bool
    {
        return !Mage::getStoreConfigFlag('catalog/search/enable_advanced_search');
    }

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function getNoRoutePath(): string
    {
        return $this->_getUrl(Mage::getStoreConfig('web/default/cms_no_route'));
    }
}
