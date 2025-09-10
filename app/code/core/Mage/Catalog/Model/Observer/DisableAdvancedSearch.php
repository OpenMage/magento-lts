<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
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
