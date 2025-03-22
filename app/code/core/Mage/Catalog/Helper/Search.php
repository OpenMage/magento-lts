<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search helper
 *
 * @category   Mage
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
