<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Catalog Observer
 *
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
