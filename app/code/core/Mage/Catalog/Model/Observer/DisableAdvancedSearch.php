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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Observer
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Observer_DisableAdvancedSearch
{
    /**
     * Disable Advanced Search at storeview scope
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Mage_Catalog_Helper_Search $helper */
        $helper = Mage::helper('catalog/search');
        if ($helper->isNotEnabled()) {
            $observer->getControllerAction()->getResponse()->setRedirect($helper->getNoRoutePath());
        }
    }
}
